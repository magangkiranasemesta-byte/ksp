<?php

namespace App\Http\Controllers;

use App\Models\ApprovalHistory;
use App\Models\Equipment;
use App\Models\EquipmentDowntime;
use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * Maintenance Request + alur approval.
 *
 *   Engineer -> PENDING_SUPERVISOR -> PENDING_MANAGER -> APPROVED -> (Work Order)
 *                      \______________ REJECTED ______________/
 *
 * Eksekusi pekerjaan (start/hold/resume/complete) TIDAK lagi dilakukan di sini,
 * melainkan di Work Order.
 */
class MaintenanceRequestController extends Controller
{
    private const TECHNICIAN_ROLES = ['ENGINEER', 'TECHNICIAN'];

    public function index(Request $request)
    {
        Gate::authorize('viewAny', MaintenanceRequest::class);

        $user = $request->user();

        $query = MaintenanceRequest::with(['equipment', 'engineer', 'workOrder'])->latest();

        // Engineer hanya melihat request miliknya.
        if (strtoupper((string) $user->role) === 'ENGINEER') {
            $query->where('engineer_id', $user->id);
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                if (ctype_digit($search)) {
                    $q->orWhere('id', (int) $search);
                }

                $q->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('priority', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('equipment', function ($equipment) use ($search) {
                        $equipment->where('name', 'like', "%{$search}%")
                            ->orWhere('equipment_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('engineer', function ($engineer) use ($search) {
                        $engineer->where('username', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->input('status')));
        }

        if ($request->filled('priority')) {
            $query->where('priority', strtoupper($request->input('priority')));
        }

        $requests = $query->paginate(7)->withQueryString();

        $equipment = Equipment::where('status', '<>', 'INACTIVE')
            ->orderBy('name')
            ->get();

        $engineers = User::whereIn('role', self::TECHNICIAN_ROLES)
            ->orderBy('username')
            ->get();

        // Prefill dari halaman Equipment / Downtime: ?equipment_id=..&open=1
        $prefillEquipmentId = $request->integer('equipment_id') ?: null;

        return view('maintenance.index', compact('requests', 'equipment', 'engineers', 'prefillEquipmentId'))
            ->with('openModal', $request->boolean('open'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', MaintenanceRequest::class);

        $user = $request->user();

        $data = $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id'],
            'engineer_id'  => ['nullable', 'exists:users,id'],
            'priority'     => ['required', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'description'  => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $equipment = Equipment::findOrFail($data['equipment_id']);

        if ($equipment->status === 'INACTIVE') {
            return back()->withInput()->with('error', 'Equipment berstatus INACTIVE tidak dapat dibuatkan request.');
        }

        // Engineer selalu atas nama dirinya sendiri (tidak bisa dimanipulasi lewat request).
        if (strtoupper((string) $user->role) === 'ENGINEER') {
            $engineerId = $user->id;
        } elseif (! empty($data['engineer_id'])) {
            $engineer = User::whereIn('role', self::TECHNICIAN_ROLES)->find($data['engineer_id']);

            if (! $engineer) {
                return back()->withInput()->with('error', 'User yang dipilih bukan Engineer.');
            }

            $engineerId = $engineer->id;
        } else {
            $engineerId = $user->id;
        }

        $maintenanceRequest = DB::transaction(function () use ($equipment, $engineerId, $data) {
            $mr = MaintenanceRequest::create([
                'equipment_id' => $equipment->id,
                'engineer_id'  => $engineerId,
                'priority'     => $data['priority'],
                'description'  => $data['description'],
                'status'       => 'PENDING_SUPERVISOR',
            ]);

            // Downtime yang sedang berjalan pada equipment ini (dan belum punya
            // request) otomatis dikaitkan, sehingga selesai bersama Work Order.
            EquipmentDowntime::where('equipment_id', $equipment->id)
                ->where('status', 'ONGOING')
                ->whereNull('maintenance_request_id')
                ->update(['maintenance_request_id' => $mr->id]);

            return $mr;
        });

        NotificationService::roles(
            ['SUPERVISOR'],
            'Maintenance Request baru',
            "Request #{$maintenanceRequest->id} untuk {$equipment->name} menunggu persetujuan Supervisor.",
            'approval',
            route('maintenance.show', $maintenanceRequest)
        );

        return redirect()
            ->route('maintenance.index')
            ->with('success', "Maintenance Request #{$maintenanceRequest->id} berhasil dibuat dan menunggu approval Supervisor.");
    }

    public function show(MaintenanceRequest $maintenanceRequest)
    {
        Gate::authorize('view', $maintenanceRequest);

        $maintenanceRequest->load([
            'equipment',
            'engineer',
            'workOrder.technician',
            'approvals' => fn ($q) => $q->with('user')->orderBy('created_at')->orderBy('id'),
        ]);

        return view('maintenance.show', compact('maintenanceRequest'));
    }

    public function approve(Request $request, MaintenanceRequest $maintenanceRequest): RedirectResponse
    {
        Gate::authorize('approve', $maintenanceRequest);

        $request->validate(['note' => ['nullable', 'string', 'max:1000']]);

        return $this->decide($request, $maintenanceRequest, 'approve');
    }

    public function reject(Request $request, MaintenanceRequest $maintenanceRequest): RedirectResponse
    {
        Gate::authorize('reject', $maintenanceRequest);

        $request->validate(['note' => ['required', 'string', 'min:5', 'max:1000']], [
            'note.required' => 'Alasan penolakan wajib diisi.',
            'note.min'      => 'Alasan penolakan minimal 5 karakter.',
        ]);

        return $this->decide($request, $maintenanceRequest, 'reject');
    }

    /**
     * Proses approve/reject secara atomik.
     * Status dibaca ulang dengan row lock agar dua approver tidak saling menimpa.
     */
    private function decide(Request $request, MaintenanceRequest $maintenanceRequest, string $action): RedirectResponse
    {
        $user = $request->user();
        $note = $request->filled('note') ? trim($request->input('note')) : null;

        $result = DB::transaction(function () use ($maintenanceRequest, $user, $action, $note) {
            $fresh = MaintenanceRequest::lockForUpdate()->findOrFail($maintenanceRequest->id);

            if (! in_array($fresh->status, ['PENDING_SUPERVISOR', 'PENDING_MANAGER'], true)) {
                return null; // sudah diproses orang lain
            }

            // Otorisasi ulang terhadap status terbaru.
            abort_unless(Gate::forUser($user)->allows($action, $fresh), 403);

            $from = $fresh->status;

            $to = $action === 'reject'
                ? 'REJECTED'
                : ($from === 'PENDING_SUPERVISOR' ? 'PENDING_MANAGER' : 'APPROVED');

            // saveQuietly: log dicatat manual di bawah agar tidak dobel.
            $fresh->forceFill(['status' => $to])->saveQuietly();

            ApprovalHistory::create([
                'maintenance_id' => $fresh->id,
                'user_id'        => $user->id,
                'role'           => strtoupper((string) $user->role),
                'action'         => $action === 'reject' ? 'REJECT' : 'APPROVE',
                'note'           => $note,
                'created_at'     => now(),
            ]);

            activity('maintenance_request')
                ->performedOn($fresh)
                ->causedBy($user)
                ->event($action)
                ->withProperties(['old_status' => $from, 'new_status' => $to, 'note' => $note])
                ->log(
                    $action === 'reject'
                        ? "Maintenance Request #{$fresh->id} ditolak"
                        : "Maintenance Request #{$fresh->id} disetujui ({$from} → {$to})"
                );

            return [$fresh, $from, $to];
        });

        if ($result === null) {
            return back()->with('error', 'Request ini sudah diproses atau tidak lagi menunggu persetujuan.');
        }

        [$fresh, $from, $to] = $result;

        $this->notifyDecision($fresh, $to, $note);

        $message = match ($to) {
            'PENDING_MANAGER' => "Request #{$fresh->id} disetujui Supervisor dan diteruskan ke Manager.",
            'APPROVED'        => "Request #{$fresh->id} disetujui. Work Order dapat dibuat.",
            default           => "Request #{$fresh->id} ditolak.",
        };

        return back()->with('success', $message);
    }

    private function notifyDecision(MaintenanceRequest $mr, string $to, ?string $note): void
    {
        if ($to === 'PENDING_MANAGER') {
            NotificationService::roles(
                ['MANAGER'],
                'Menunggu persetujuan Manager',
                "Request #{$mr->id} telah disetujui Supervisor.",
                'approval',
                route('maintenance.show', $mr)
            );

            return;
        }

        if ($to === 'APPROVED') {
            NotificationService::user(
                $mr->engineer,
                'Maintenance Request disetujui',
                "Request #{$mr->id} telah disetujui sepenuhnya.",
                'success',
                route('maintenance.show', $mr)
            );

            NotificationService::roles(
                ['ADMIN'],
                'Request siap dibuatkan Work Order',
                "Request #{$mr->id} sudah APPROVED.",
                'maintenance',
                route('work-orders.create', ['maintenance_request_id' => $mr->id])
            );

            return;
        }

        NotificationService::user(
            $mr->engineer,
            'Maintenance Request ditolak',
            "Request #{$mr->id} ditolak" . ($note ? ": {$note}" : '.'),
            'warning',
            route('maintenance.show', $mr)
        );
    }
}
