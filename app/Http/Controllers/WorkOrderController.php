<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * Work Order.
 *
 *   OPEN -> ASSIGNED -> IN_PROGRESS -> COMPLETED
 *                          |  ^
 *                          v  |
 *                         ON_HOLD
 *   OPEN / ASSIGNED / ON_HOLD -> CANCELLED
 */
class WorkOrderController extends Controller
{
    private const TECHNICIAN_ROLES = ['ENGINEER', 'TECHNICIAN'];

    /*
    |--------------------------------------------------------------------------
    | CRUD
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        Gate::authorize('viewAny', WorkOrder::class);

        $base = $this->visibleQuery($request->user());

        $query = (clone $base)->with(['equipment', 'technician', 'maintenanceRequest']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('wo_number', 'like', "%{$search}%")
                    ->orWhere('maintenance_type', 'like', "%{$search}%")
                    ->orWhere('priority', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('equipment', function ($equipment) use ($search) {
                        $equipment->where('name', 'like', "%{$search}%")
                            ->orWhere('equipment_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('technician', function ($technician) use ($search) {
                        $technician->where('username', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $workOrders = $query->latest()->paginate(10)->withQueryString();

        $totalWorkOrders      = (clone $base)->count();
        $openWorkOrders       = (clone $base)->whereIn('status', ['OPEN', 'ASSIGNED'])->count();
        $inProgressWorkOrders = (clone $base)->where('status', 'IN_PROGRESS')->count();
        $completedWorkOrders  = (clone $base)->where('status', 'COMPLETED')->count();
        $onHoldWorkOrders     = (clone $base)->where('status', 'ON_HOLD')->count();

        return view('work-orders.index', compact(
            'workOrders',
            'totalWorkOrders',
            'openWorkOrders',
            'inProgressWorkOrders',
            'completedWorkOrders',
            'onHoldWorkOrders'
        ));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', WorkOrder::class);

        $equipment   = Equipment::orderBy('name')->get();
        $technicians = $this->technicians();

        // Hanya Maintenance Request APPROVED yang belum punya Work Order.
        $maintenanceRequests = MaintenanceRequest::with('equipment')
            ->where('status', 'APPROVED')
            ->whereDoesntHave('workOrder')
            ->latest()
            ->get();

        $selectedRequest = null;

        if ($request->filled('maintenance_request_id')) {
            $selectedRequest = MaintenanceRequest::with('equipment')
                ->whereKey($request->input('maintenance_request_id'))
                ->where('status', 'APPROVED')
                ->whereDoesntHave('workOrder')
                ->first();
        }

        return view('work-orders.create', compact(
            'equipment',
            'technicians',
            'maintenanceRequests',
            'selectedRequest'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', WorkOrder::class);

        $validated = $request->validate(array_merge(
            ['maintenance_request_id' => ['required', 'exists:maintenance_requests,id']],
            $this->fieldRules()
        ));

        $technician = null;

        if (! empty($validated['technician_id'])) {
            $technician = $this->findTechnician((int) $validated['technician_id']);

            if (! $technician) {
                return back()->withInput()->with('warning', 'User yang dipilih bukan Engineer atau Technician.');
            }
        }

        // Semua pengecekan dilakukan di dalam transaksi dengan row lock pada
        // Maintenance Request, sehingga tidak mungkin terbentuk WO ganda.
        $outcome = DB::transaction(function () use ($validated, $technician) {
            $mr = MaintenanceRequest::lockForUpdate()->findOrFail($validated['maintenance_request_id']);

            if ($mr->status !== 'APPROVED') {
                return ['error' => 'Work Order hanya dapat dibuat dari Maintenance Request yang sudah APPROVED.'];
            }

            $existing = WorkOrder::where('maintenance_request_id', $mr->id)->first();

            if ($existing) {
                return ['existing' => $existing];
            }

            $workOrder = WorkOrder::create([
                'wo_number'              => $this->generateWoNumber(),
                'maintenance_request_id' => $mr->id,
                'equipment_id'           => $mr->equipment_id, // selalu mengikuti request
                'technician_id'          => $technician?->id,
                'maintenance_type'       => $validated['maintenance_type'],
                'priority'               => $validated['priority'],
                'problem_description'    => $validated['problem_description'] ?? $mr->description,
                'root_cause'             => $validated['root_cause'] ?? null,
                'corrective_action'      => $validated['corrective_action'] ?? null,
                'planned_start'          => $validated['planned_start'] ?? null,
                'planned_end'            => $validated['planned_end'] ?? null,
                'status'                 => $technician ? 'ASSIGNED' : 'OPEN',
                'completion_notes'       => $validated['completion_notes'] ?? null,
            ]);

            return ['workOrder' => $workOrder];
        });

        if (isset($outcome['error'])) {
            return back()->withInput()->with('warning', $outcome['error']);
        }

        if (isset($outcome['existing'])) {
            return redirect()
                ->route('work-orders.show', $outcome['existing'])
                ->with('warning', 'Maintenance Request tersebut sudah memiliki Work Order.');
        }

        $workOrder = $outcome['workOrder'];

        if ($technician) {
            $this->notifyAssigned($workOrder, $technician);
        }

        return redirect()
            ->route('work-orders.show', $workOrder)
            ->with('success', "Work Order {$workOrder->wo_number} berhasil dibuat.");
    }

    public function show(WorkOrder $workOrder)
    {
        Gate::authorize('view', $workOrder);

        $workOrder->load(['equipment', 'technician', 'maintenanceRequest']);

        $technicians = Gate::allows('assign', $workOrder) ? $this->technicians() : collect();

        return view('work-orders.show', compact('workOrder', 'technicians'));
    }

    public function edit(WorkOrder $workOrder)
    {
        Gate::authorize('update', $workOrder);

        if (! $this->isEditable($workOrder)) {
            return redirect()
                ->route('work-orders.show', $workOrder)
                ->with('warning', 'Work Order hanya dapat diedit ketika status OPEN atau ASSIGNED.');
        }

        $workOrder->load(['equipment', 'technician', 'maintenanceRequest']);

        $equipment   = Equipment::orderBy('name')->get();
        $technicians = $this->technicians();

        // Request milik WO ini harus tetap tampil agar nilainya tidak kosong saat submit.
        $maintenanceRequests = MaintenanceRequest::with('equipment')
            ->where(function ($query) use ($workOrder) {
                $query->where('status', 'APPROVED')
                    ->orWhere('id', $workOrder->maintenance_request_id);
            })
            ->where(function ($query) use ($workOrder) {
                $query->whereDoesntHave('workOrder')
                    ->orWhere('id', $workOrder->maintenance_request_id);
            })
            ->latest()
            ->get();

        return view('work-orders.edit', compact(
            'workOrder',
            'equipment',
            'technicians',
            'maintenanceRequests'
        ));
    }

    public function update(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        Gate::authorize('update', $workOrder);

        if (! $this->isEditable($workOrder)) {
            return redirect()
                ->route('work-orders.show', $workOrder)
                ->with('warning', 'Work Order hanya dapat diedit ketika status OPEN atau ASSIGNED.');
        }

        $validated = $request->validate(array_merge(
            ['maintenance_request_id' => ['required', 'exists:maintenance_requests,id']],
            $this->fieldRules()
        ));

        $maintenanceRequest = MaintenanceRequest::findOrFail($validated['maintenance_request_id']);

        if ($maintenanceRequest->status !== 'APPROVED') {
            return back()->withInput()->with('warning', 'Work Order hanya dapat menggunakan Maintenance Request yang sudah APPROVED.');
        }

        $usedByOther = WorkOrder::where('maintenance_request_id', $maintenanceRequest->id)
            ->where('id', '!=', $workOrder->id)
            ->exists();

        if ($usedByOther) {
            return back()->withInput()->with('warning', 'Maintenance Request tersebut sudah digunakan oleh Work Order lain.');
        }

        $technician = null;

        if (! empty($validated['technician_id'])) {
            $technician = $this->findTechnician((int) $validated['technician_id']);

            if (! $technician) {
                return back()->withInput()->with('warning', 'User yang dipilih bukan Engineer atau Technician.');
            }
        }

        $previousTechnicianId = $workOrder->technician_id;

        // Status mengikuti keberadaan technician (OPEN <-> ASSIGNED).
        $workOrder->update([
            'maintenance_request_id' => $maintenanceRequest->id,
            'equipment_id'           => $maintenanceRequest->equipment_id,
            'technician_id'          => $technician?->id,
            'status'                 => $technician ? 'ASSIGNED' : 'OPEN',
            'maintenance_type'       => $validated['maintenance_type'],
            'priority'               => $validated['priority'],
            'problem_description'    => $validated['problem_description'] ?? null,
            'root_cause'             => $validated['root_cause'] ?? null,
            'corrective_action'      => $validated['corrective_action'] ?? null,
            'planned_start'          => $validated['planned_start'] ?? null,
            'planned_end'            => $validated['planned_end'] ?? null,
            'completion_notes'       => $validated['completion_notes'] ?? null,
        ]);

        if ($technician && (int) $previousTechnicianId !== (int) $technician->id) {
            $this->notifyAssigned($workOrder, $technician);
        }

        return redirect()
            ->route('work-orders.show', $workOrder)
            ->with('success', "Work Order {$workOrder->wo_number} berhasil diperbarui.");
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow actions
    |--------------------------------------------------------------------------
    */

    public function assign(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        Gate::authorize('assign', $workOrder);

        $validated = $request->validate([
            'technician_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $technician = $this->findTechnician((int) $validated['technician_id']);

        if (! $technician) {
            return back()->with('warning', 'User yang dipilih bukan Engineer atau Technician.');
        }

        $error = $this->transition(
            $workOrder,
            'assign',
            ['OPEN', 'ASSIGNED'],
            ['technician_id' => $technician->id, 'status' => 'ASSIGNED'],
            "Work Order {$workOrder->wo_number} ditugaskan kepada {$technician->username}",
            ['technician' => $technician->username]
        );

        if ($error) {
            return back()->with('warning', $error);
        }

        $this->notifyAssigned($workOrder, $technician);

        return back()->with('success', "Work Order {$workOrder->wo_number} berhasil di-assign kepada {$technician->username}.");
    }

    public function start(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        Gate::authorize('start', $workOrder);

        if (! $workOrder->technician_id) {
            return back()->with('warning', 'Work Order belum memiliki Technician/Engineer.');
        }

        if ($workOrder->equipment && $workOrder->equipment->status === 'INACTIVE') {
            return back()->with('warning', 'Equipment berstatus INACTIVE, Work Order tidak dapat dimulai.');
        }

        $error = $this->transition(
            $workOrder,
            'start',
            ['ASSIGNED'],
            ['status' => 'IN_PROGRESS', 'actual_start' => now()],
            "Work Order {$workOrder->wo_number} mulai dikerjakan"
        );

        if ($error) {
            return back()->with('warning', $error);
        }

        return back()->with('success', "Work Order {$workOrder->wo_number} mulai dikerjakan.");
    }

    public function hold(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        Gate::authorize('hold', $workOrder);

        $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $error = $this->transition(
            $workOrder,
            'hold',
            ['IN_PROGRESS'],
            ['status' => 'ON_HOLD'],
            "Work Order {$workOrder->wo_number} ditunda",
            ['reason' => $request->input('reason')]
        );

        if ($error) {
            return back()->with('warning', $error);
        }

        return back()->with('success', "Work Order {$workOrder->wo_number} berhasil ditunda.");
    }

    public function resume(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        Gate::authorize('resume', $workOrder);

        $error = $this->transition(
            $workOrder,
            'resume',
            ['ON_HOLD'],
            ['status' => 'IN_PROGRESS'],
            "Work Order {$workOrder->wo_number} dilanjutkan kembali"
        );

        if ($error) {
            return back()->with('warning', $error);
        }

        return back()->with('success', "Work Order {$workOrder->wo_number} dilanjutkan kembali.");
    }

    public function complete(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        Gate::authorize('complete', $workOrder);

        $validated = $request->validate(
            ['completion_notes' => ['required', 'string', 'min:5', 'max:5000']],
            [
                'completion_notes.required' => 'Catatan penyelesaian wajib diisi.',
                'completion_notes.min'      => 'Catatan penyelesaian minimal 5 karakter.',
            ]
        );

        $error = $this->transition(
            $workOrder,
            'complete',
            ['IN_PROGRESS'],
            [
                'status'           => 'COMPLETED',
                'actual_end'       => now(),
                'completion_notes' => $validated['completion_notes'],
            ],
            "Work Order {$workOrder->wo_number} diselesaikan",
            ['completion_notes' => $validated['completion_notes']]
        );

        if ($error) {
            return back()->with('warning', $error);
        }

        $this->notifyCompleted($workOrder);

        return back()->with('success', "Work Order {$workOrder->wo_number} berhasil diselesaikan.");
    }

    public function cancel(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        Gate::authorize('cancel', $workOrder);

        $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $error = $this->transition(
            $workOrder,
            'cancel',
            ['OPEN', 'ASSIGNED', 'ON_HOLD'],
            ['status' => 'CANCELLED'],
            "Work Order {$workOrder->wo_number} dibatalkan",
            ['reason' => $request->input('reason')]
        );

        if ($error) {
            return back()->with('warning', $error);
        }

        return back()->with('success', "Work Order {$workOrder->wo_number} berhasil dibatalkan.");
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Satu pintu untuk semua perubahan status Work Order.
     *
     * - Row lock + cek ulang status (mencegah double submit / race).
     * - Update status tanpa event Eloquent, lalu catat satu activity log
     *   bernama jelas (assign/start/hold/resume/complete/cancel).
     * - Sinkronisasi status Equipment dalam transaksi yang sama.
     *
     * @return string|null pesan error, atau null jika berhasil
     */
    private function transition(
        WorkOrder $workOrder,
        string $event,
        array $allowedFrom,
        array $changes,
        string $description,
        array $properties = []
    ): ?string {
        $user = auth()->user();

        return DB::transaction(function () use ($workOrder, $event, $allowedFrom, $changes, $description, $properties, $user) {
            $fresh = WorkOrder::lockForUpdate()->findOrFail($workOrder->id);

            if (! in_array($fresh->status, $allowedFrom, true)) {
                return 'Aksi tidak dapat dilakukan. Status Work Order saat ini: '
                    . str_replace('_', ' ', $fresh->status)
                    . ' (dibutuhkan: ' . str_replace('_', ' ', implode(' / ', $allowedFrom)) . ').';
            }

            $oldStatus = $fresh->status;

            $fresh->forceFill($changes)->saveQuietly();

            $this->syncEquipmentStatus((int) $fresh->equipment_id);

            activity('work_order')
                ->performedOn($fresh)
                ->causedBy($user)
                ->event($event)
                ->withProperties(array_merge(
                    ['old_status' => $oldStatus, 'new_status' => $fresh->status],
                    array_filter($properties, fn ($v) => $v !== null && $v !== '')
                ))
                ->log($description);

            // Segarkan instance milik route agar notifikasi/redirect memakai data terbaru.
            $workOrder->setRawAttributes($fresh->getAttributes(), true);

            return null;
        });
    }

    /**
     * Status equipment mengikuti Work Order yang sedang berjalan:
     * ada WO IN_PROGRESS/ON_HOLD  -> MAINTENANCE
     * tidak ada & status MAINTENANCE -> ACTIVE
     * INACTIVE tidak pernah diubah.
     */
    private function syncEquipmentStatus(int $equipmentId): void
    {
        $equipment = Equipment::lockForUpdate()->find($equipmentId);

        if (! $equipment || $equipment->status === 'INACTIVE') {
            return;
        }

        $hasActiveWork = WorkOrder::where('equipment_id', $equipmentId)
            ->whereIn('status', ['IN_PROGRESS', 'ON_HOLD'])
            ->exists();

        $target = $hasActiveWork
            ? 'MAINTENANCE'
            : ($equipment->status === 'MAINTENANCE' ? 'ACTIVE' : $equipment->status);

        if ($equipment->status !== $target) {
            $equipment->status = $target;
            $equipment->save();
        }
    }

    private function visibleQuery(User $user): Builder
    {
        $query = WorkOrder::query();

        if (strtoupper((string) $user->role) === 'ENGINEER') {
            $query->where('technician_id', $user->id);
        }

        return $query;
    }

    private function technicians()
    {
        return User::whereIn('role', self::TECHNICIAN_ROLES)->orderBy('username')->get();
    }

    private function findTechnician(int $id): ?User
    {
        return User::whereIn('role', self::TECHNICIAN_ROLES)->find($id);
    }

    private function isEditable(WorkOrder $workOrder): bool
    {
        return in_array($workOrder->status, ['OPEN', 'ASSIGNED'], true);
    }

    private function fieldRules(): array
    {
        return [
            'technician_id'       => ['nullable', 'exists:users,id'],
            'maintenance_type'    => ['required', 'in:CORRECTIVE,PREVENTIVE,INSPECTION'],
            'priority'            => ['required', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'problem_description' => ['nullable', 'string'],
            'root_cause'          => ['nullable', 'string'],
            'corrective_action'   => ['nullable', 'string'],
            'planned_start'       => ['nullable', 'date'],
            'planned_end'         => ['nullable', 'date', 'after_or_equal:planned_start'],
            'completion_notes'    => ['nullable', 'string'],
        ];
    }

    private function notifyAssigned(WorkOrder $workOrder, User $technician): void
    {
        NotificationService::user(
            $technician,
            'Work Order ditugaskan',
            "{$workOrder->wo_number} ditugaskan kepada Anda.",
            'maintenance',
            route('work-orders.show', $workOrder)
        );
    }

    private function notifyCompleted(WorkOrder $workOrder): void
    {
        $url = route('work-orders.show', $workOrder);

        NotificationService::roles(
            ['SUPERVISOR', 'MANAGER'],
            'Work Order selesai',
            "{$workOrder->wo_number} telah diselesaikan.",
            'success',
            $url
        );

        $requester = $workOrder->maintenanceRequest?->engineer;

        if ($requester && (int) $requester->id !== (int) auth()->id()) {
            NotificationService::user(
                $requester,
                'Work Order selesai',
                "{$workOrder->wo_number} untuk request Anda telah selesai.",
                'success',
                $url
            );
        }
    }

    /**
     * Format: WO-YYYYMMDD-0001. Dipanggil di dalam transaksi.
     * Kolom wo_number UNIQUE menjadi pengaman terakhir.
     */
    private function generateWoNumber(): string
    {
        $prefix = 'WO-' . now()->format('Ymd') . '-';

        $last = WorkOrder::where('wo_number', 'like', $prefix . '%')
            ->orderByDesc('wo_number')
            ->lockForUpdate()
            ->first();

        $sequence = $last ? ((int) substr($last->wo_number, -4)) + 1 : 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
