<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\PreventiveMaintenance;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PreventiveMaintenanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Jadwal Preventive Maintenance.
 *
 * Siklus:  jadwal (due) -> Generate Work Order -> WO selesai
 *          -> riwayat tercatat + jadwal berikutnya digeser otomatis.
 */
class PreventiveMaintenanceController extends Controller
{
    private const TECHNICIAN_ROLES = ['ENGINEER', 'TECHNICIAN'];

    public function __construct(private PreventiveMaintenanceService $service)
    {
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', PreventiveMaintenance::class);

        $query = PreventiveMaintenance::with(['equipment', 'technician'])
            ->orderBy('next_maintenance_date')
            ->orderBy('id');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('equipment', function ($e) use ($search) {
                        $e->where('name', 'like', "%{$search}%")
                            ->orWhere('equipment_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('technician', fn ($t) => $t->where('username', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('frequency') && array_key_exists($request->input('frequency'), PreventiveMaintenance::FREQUENCIES)) {
            $query->where('frequency', $request->input('frequency'));
        }

        match ($request->input('state')) {
            'overdue'     => $query->overdue(),
            'due_soon'    => $query->dueSoon(),
            'safe'        => $query->safe(),
            'in_progress' => $query->where('status', 'in_progress'),
            default       => null,
        };

        $preventives = $query->paginate(10)->withQueryString();

        $counts = [
            'total'       => PreventiveMaintenance::count(),
            'overdue'     => PreventiveMaintenance::overdue()->count(),
            'due_soon'    => PreventiveMaintenance::dueSoon()->count(),
            'in_progress' => PreventiveMaintenance::where('status', 'in_progress')->count(),
        ];

        $equipments = Equipment::where('status', '<>', 'INACTIVE')->orderBy('name')->get();
        $engineers  = $this->technicians();

        return view('maintenance.preventive', compact('preventives', 'counts', 'equipments', 'engineers'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', PreventiveMaintenance::class);

        $data = $this->validated($request);

        $equipment = Equipment::findOrFail($data['equipment_id']);

        if ($equipment->status === 'INACTIVE') {
            return back()->withInput()->with('error', 'Equipment berstatus INACTIVE tidak dapat dijadwalkan.');
        }

        $preventive = PreventiveMaintenance::create($data + ['status' => 'scheduled']);

        return redirect()
            ->route('maintenance.preventive.show', $preventive)
            ->with('success', 'Jadwal Preventive Maintenance berhasil dibuat.');
    }

    public function show(PreventiveMaintenance $preventive)
    {
        Gate::authorize('view', $preventive);

        $preventive->load([
            'equipment',
            'technician',
            'logs' => fn ($q) => $q->with(['performer', 'workOrder'])->latest('performed_at')->limit(20),
            'workOrders' => fn ($q) => $q->latest()->limit(10),
        ]);

        $openWorkOrder = $preventive->workOrders
            ->first(fn ($wo) => in_array($wo->status, PreventiveMaintenanceService::OPEN_WO_STATUSES, true));

        return view('preventive.show', compact('preventive', 'openWorkOrder'));
    }

    public function edit(PreventiveMaintenance $preventive)
    {
        Gate::authorize('update', $preventive);

        return view('preventive.edit', [
            'preventive' => $preventive,
            'equipments' => Equipment::orderBy('name')->get(),
            'engineers'  => $this->technicians(),
        ]);
    }

    public function update(Request $request, PreventiveMaintenance $preventive): RedirectResponse
    {
        Gate::authorize('update', $preventive);

        $data = $this->validated($request);

        if ((int) $data['equipment_id'] !== (int) $preventive->equipment_id
            && $this->service->hasOpenWorkOrder($preventive)) {
            return back()->withInput()->with('error', 'Equipment tidak dapat diganti selama Work Order jadwal ini masih berjalan.');
        }

        $preventive->update($data);

        return redirect()
            ->route('maintenance.preventive.show', $preventive)
            ->with('success', 'Jadwal Preventive Maintenance berhasil diperbarui.');
    }

    /**
     * Penyelesaian manual (tanpa Work Order).
     */
    public function complete(Request $request, PreventiveMaintenance $preventive): RedirectResponse
    {
        Gate::authorize('complete', $preventive);

        $data = $request->validate(['notes' => ['nullable', 'string', 'max:2000']]);

        if ($this->service->hasOpenWorkOrder($preventive)) {
            return back()->with('error', 'Jadwal ini memiliki Work Order yang masih berjalan. Selesaikan melalui Work Order.');
        }

        $this->service->complete($preventive, $request->user(), null, $data['notes'] ?? null);

        return back()->with('success', 'Preventive Maintenance dicatat selesai. Jadwal berikutnya sudah diperbarui.');
    }

    public function generateWorkOrder(Request $request, PreventiveMaintenance $preventive): RedirectResponse
    {
        Gate::authorize('generateWorkOrder', $preventive);

        [$workOrder, $error] = $this->service->generateWorkOrder($preventive, $request->user());

        if ($error) {
            return back()->with('warning', $error);
        }

        if ($workOrder->technician_id) {
            NotificationService::user(
                $workOrder->technician,
                'Work Order Preventive ditugaskan',
                "{$workOrder->wo_number} ({$preventive->title}) ditugaskan kepada Anda.",
                'maintenance',
                route('work-orders.show', $workOrder)
            );
        }

        return redirect()
            ->route('work-orders.show', $workOrder)
            ->with('success', "Work Order {$workOrder->wo_number} dibuat dari jadwal preventive.");
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'equipment_id'          => ['required', 'exists:equipment,id'],
            'title'                 => ['required', 'string', 'max:255'],
            'frequency'             => ['required', 'in:' . implode(',', array_keys(PreventiveMaintenance::FREQUENCIES))],
            'last_maintenance_date' => ['nullable', 'date', 'before_or_equal:today'],
            'next_maintenance_date' => ['required', 'date'],
            'assigned_to'           => ['nullable', 'exists:users,id'],
            'notes'                 => ['nullable', 'string', 'max:2000'],
        ]);

        if (! empty($data['assigned_to'])
            && ! User::whereIn('role', self::TECHNICIAN_ROLES)->whereKey($data['assigned_to'])->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'assigned_to' => 'User yang dipilih bukan Engineer/Technician.',
            ]);
        }

        return $data + ['assigned_to' => null, 'notes' => null, 'last_maintenance_date' => null];
    }

    private function technicians()
    {
        return User::whereIn('role', self::TECHNICIAN_ROLES)->orderBy('username')->get();
    }
}
