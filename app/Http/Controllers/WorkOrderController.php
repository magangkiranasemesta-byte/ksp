<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of work orders.
     */
    public function index(Request $request)
    {
        $query = WorkOrder::with([
            'equipment',
            'technician',
            'maintenanceRequest',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->search;

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

        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Priority
        |--------------------------------------------------------------------------
        */

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $workOrders = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalWorkOrders = WorkOrder::count();

        $openWorkOrders = WorkOrder::whereIn('status', [
            'OPEN',
            'ASSIGNED',
        ])->count();

        $inProgressWorkOrders = WorkOrder::where(
            'status',
            'IN_PROGRESS'
        )->count();

        $completedWorkOrders = WorkOrder::where(
            'status',
            'COMPLETED'
        )->count();

        $onHoldWorkOrders = WorkOrder::where(
            'status',
            'ON_HOLD'
        )->count();

        return view('work-orders.index', compact(
            'workOrders',
            'totalWorkOrders',
            'openWorkOrders',
            'inProgressWorkOrders',
            'completedWorkOrders',
            'onHoldWorkOrders'
        ));
    }


    /**
     * Show the form for creating a new work order.
     */
    public function create(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Equipment
        |--------------------------------------------------------------------------
        */

        $equipment = Equipment::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | Technician / Engineer
        |--------------------------------------------------------------------------
        */

        $technicians = User::whereIn('role', [
            'ENGINEER',
            'TECHNICIAN',
        ])
            ->orderBy('username')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Maintenance Requests
        |--------------------------------------------------------------------------
        |
        | Hanya request yang belum mempunyai Work Order.
        |
        */

        $maintenanceRequests = MaintenanceRequest::with('equipment')
            ->whereDoesntHave('workOrder')
            ->whereNotIn('status', [
                'REJECTED',
                'CANCELLED',
            ])
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Selected Maintenance Request
        |--------------------------------------------------------------------------
        */

        $selectedRequest = null;

        if ($request->filled('maintenance_request_id')) {
            $selectedRequest = MaintenanceRequest::with('equipment')
                ->whereKey($request->maintenance_request_id)
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

    public function edit(WorkOrder $workOrder)
    {
        $workOrder->load([
            'equipment',
            'technician',
            'maintenanceRequest',
        ]);

        $equipment = Equipment::orderBy('name')->get();

        $technicians = User::whereIn('role', [
            'ENGINEER',
            'TECHNICIAN',
        ])
            ->orderBy('username')
            ->get();

        $maintenanceRequests = MaintenanceRequest::with('equipment')
            ->where(function ($query) use ($workOrder) {
                $query->whereDoesntHave('workOrder')
                    ->orWhereKey($workOrder->maintenance_request_id);
            })
            ->whereNotIn('status', [
                'REJECTED',
                'CANCELLED',
            ])
            ->latest()
            ->get();

        return view('work-orders.edit', compact(
            'workOrder',
            'equipment',
            'technicians',
            'maintenanceRequests'
        ));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'maintenance_request_id' => [
                'nullable',
                'exists:maintenance_requests,id',
            ],

            'equipment_id' => [
                'required',
                'exists:equipment,id',
            ],

            'technician_id' => [
                'nullable',
                'exists:users,id',
            ],

            'maintenance_type' => [
                'required',
                'in:CORRECTIVE,PREVENTIVE,INSPECTION',
            ],

            'priority' => [
                'required',
                'in:LOW,MEDIUM,HIGH,CRITICAL',
            ],

            'problem_description' => [
                'nullable',
                'string',
            ],

            'root_cause' => [
                'nullable',
                'string',
            ],

            'corrective_action' => [
                'nullable',
                'string',
            ],

            'planned_start' => [
                'nullable',
                'date',
            ],

            'planned_end' => [
                'nullable',
                'date',
                'after_or_equal:planned_start',
            ],

            'actual_start' => [
                'nullable',
                'date',
            ],

            'actual_end' => [
                'nullable',
                'date',
                'after_or_equal:actual_start',
            ],

            'status' => [
                'required',
                'in:OPEN,ASSIGNED,IN_PROGRESS,ON_HOLD,COMPLETED,CANCELLED',
            ],

            'completion_notes' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cegah satu Maintenance Request memiliki lebih dari satu Work Order
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['maintenance_request_id'])) {

            $existingWorkOrder = WorkOrder::where(
                'maintenance_request_id',
                $validated['maintenance_request_id']
            )
                ->whereKey('!=', $workOrder->id)
                ->first();

            if ($existingWorkOrder) {
                return back()
                    ->withInput()
                    ->with(
                        'warning',
                        'Maintenance Request tersebut sudah digunakan oleh Work Order lain.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Update Work Order
        |--------------------------------------------------------------------------
        */

        $workOrder->update($validated);

        return redirect()
            ->route('work-orders.show', $workOrder)
            ->with(
                'success',
                "Work Order {$workOrder->wo_number} berhasil diperbarui."
            );
    }


    /**
     * Store a newly created work order.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'maintenance_request_id' => [
                'nullable',
                'exists:maintenance_requests,id',
            ],

            'equipment_id' => [
                'required',
                'exists:equipment,id',
            ],

            'technician_id' => [
                'nullable',
                'exists:users,id',
            ],

            'maintenance_type' => [
                'required',
                'in:CORRECTIVE,PREVENTIVE,INSPECTION',
            ],

            'priority' => [
                'required',
                'in:LOW,MEDIUM,HIGH,CRITICAL',
            ],

            'problem_description' => [
                'nullable',
                'string',
            ],

            'root_cause' => [
                'nullable',
                'string',
            ],

            'corrective_action' => [
                'nullable',
                'string',
            ],

            'planned_start' => [
                'nullable',
                'date',
            ],

            'planned_end' => [
                'nullable',
                'date',
                'after_or_equal:planned_start',
            ],

            'completion_notes' => [
                'nullable',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Work Order
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['maintenance_request_id'])) {

            $existingWorkOrder = WorkOrder::where(
                'maintenance_request_id',
                $validated['maintenance_request_id']
            )->first();

            if ($existingWorkOrder) {
                return redirect()
                    ->route('work-orders.show', $existingWorkOrder)
                    ->with(
                        'warning',
                        'Maintenance Request tersebut sudah memiliki Work Order.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Create Work Order
        |--------------------------------------------------------------------------
        */

        $workOrder = DB::transaction(function () use ($validated) {

            $validated['wo_number'] = $this->generateWoNumber();

            $validated['status'] = 'OPEN';

            return WorkOrder::create($validated);
        });


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('work-orders.show', $workOrder)
            ->with(
                'success',
                "Work Order {$workOrder->wo_number} berhasil dibuat."
            );
    }


    /**
     * Display the specified work order.
     */
    public function show(WorkOrder $workOrder)
    {
        $workOrder->load([
            'equipment',
            'technician',
            'maintenanceRequest',
        ]);

        return view(
            'work-orders.show',
            compact('workOrder')
        );
    }


    /**
     * Generate Work Order Number.
     *
     * Format:
     * WO-YYYYMMDD-0001
     */
    private function generateWoNumber(): string
    {
        $prefix = 'WO-' . now()->format('Ymd') . '-';

        $lastWorkOrder = WorkOrder::where(
            'wo_number',
            'like',
            $prefix . '%'
        )
            ->orderByDesc('wo_number')
            ->lockForUpdate()
            ->first();

        if (!$lastWorkOrder) {
            $sequence = 1;
        } else {
            $lastSequence = (int) substr(
                $lastWorkOrder->wo_number,
                -4
            );

            $sequence = $lastSequence + 1;
        }

        return $prefix . str_pad(
            $sequence,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
}