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

                        $equipment
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere(
                                'equipment_code',
                                'like',
                                "%{$search}%"
                            );
                    })

                    ->orWhereHas('technician', function ($technician) use ($search) {

                        $technician->where(
                            'username',
                            'like',
                            "%{$search}%"
                        );
                    });
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Priority
        |--------------------------------------------------------------------------
        */

        if ($request->filled('priority')) {

            $query->where(
                'priority',
                $request->priority
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

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

        $openWorkOrders = WorkOrder::whereIn(
            'status',
            [
                'OPEN',
                'ASSIGNED',
            ]
        )->count();

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


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'work-orders.index',
            compact(
                'workOrders',
                'totalWorkOrders',
                'openWorkOrders',
                'inProgressWorkOrders',
                'completedWorkOrders',
                'onHoldWorkOrders'
            )
        );
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
        |
        | Tetap disediakan jika create WO manual masih digunakan.
        |
        */

        $equipment = Equipment::orderBy('name')->get();


        /*
        |--------------------------------------------------------------------------
        | Technician / Engineer
        |--------------------------------------------------------------------------
        */

        $technicians = User::whereIn(
            'role',
            [
                'ENGINEER',
                'TECHNICIAN',
            ]
        )
            ->orderBy('username')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Maintenance Requests
        |--------------------------------------------------------------------------
        |
        | Hanya Maintenance Request yang sudah APPROVED
        | dan belum mempunyai Work Order.
        |
        */

        $maintenanceRequests = MaintenanceRequest::with('equipment')
            ->where('status', 'APPROVED')
            ->whereDoesntHave('workOrder')
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
                ->where('status', 'APPROVED')
                ->whereDoesntHave('workOrder')
                ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'work-orders.create',
            compact(
                'equipment',
                'technicians',
                'maintenanceRequests',
                'selectedRequest'
            )
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
                'required',
                'exists:maintenance_requests,id',
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
        | Get Maintenance Request
        |--------------------------------------------------------------------------
        */

        $maintenanceRequest = MaintenanceRequest::with('equipment')
            ->findOrFail(
                $validated['maintenance_request_id']
            );


        /*
        |--------------------------------------------------------------------------
        | Check Maintenance Request Status
        |--------------------------------------------------------------------------
        */

        if ($maintenanceRequest->status !== 'APPROVED') {

            return back()
                ->withInput()
                ->with(
                    'warning',
                    'Work Order hanya dapat dibuat dari Maintenance Request yang sudah APPROVED.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Work Order
        |--------------------------------------------------------------------------
        */

        $existingWorkOrder = WorkOrder::where(
            'maintenance_request_id',
            $maintenanceRequest->id
        )->first();

        if ($existingWorkOrder) {

            return redirect()
                ->route(
                    'work-orders.show',
                    $existingWorkOrder
                )
                ->with(
                    'warning',
                    'Maintenance Request tersebut sudah memiliki Work Order.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Technician
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['technician_id'])) {

            $technician = User::whereIn(
                'role',
                [
                    'ENGINEER',
                    'TECHNICIAN',
                ]
            )
                ->where(
                    'id',
                    $validated['technician_id']
                )
                ->first();

            if (!$technician) {

                return back()
                    ->withInput()
                    ->with(
                        'warning',
                        'User yang dipilih bukan Engineer atau Technician.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Create Work Order
        |--------------------------------------------------------------------------
        */

        $workOrder = DB::transaction(function () use (
            $validated,
            $maintenanceRequest
        ) {

            /*
            |--------------------------------------------------------------------------
            | Generate WO Number
            |--------------------------------------------------------------------------
            */

            $woNumber = $this->generateWoNumber();


            /*
            |--------------------------------------------------------------------------
            | Determine Status
            |--------------------------------------------------------------------------
            |
            | Jika technician langsung dipilih:
            | OPEN → ASSIGNED
            |
            | Jika technician belum dipilih:
            | OPEN
            |
            */

            $status = !empty($validated['technician_id'])
                ? 'ASSIGNED'
                : 'OPEN';


            /*
            |--------------------------------------------------------------------------
            | Create
            |--------------------------------------------------------------------------
            */

            return WorkOrder::create([

                'wo_number' => $woNumber,

                'maintenance_request_id' =>
                    $maintenanceRequest->id,

                /*
                | Equipment selalu mengikuti
                | Maintenance Request.
                */
                'equipment_id' =>
                    $maintenanceRequest->equipment_id,

                'technician_id' =>
                    $validated['technician_id'] ?? null,

                'maintenance_type' =>
                    $validated['maintenance_type'],

                'priority' =>
                    $validated['priority'],

                'problem_description' =>
                    $validated['problem_description'] ?? null,

                'root_cause' =>
                    $validated['root_cause'] ?? null,

                'corrective_action' =>
                    $validated['corrective_action'] ?? null,

                'planned_start' =>
                    $validated['planned_start'] ?? null,

                'planned_end' =>
                    $validated['planned_end'] ?? null,

                'status' =>
                    $status,

                'completion_notes' =>
                    $validated['completion_notes'] ?? null,
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'work-orders.show',
                $workOrder
            )
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
     * Show the form for editing the specified work order.
     */
    public function edit(WorkOrder $workOrder)
    {
        /*
        |--------------------------------------------------------------------------
        | Load Relations
        |--------------------------------------------------------------------------
        */

        $workOrder->load([
            'equipment',
            'technician',
            'maintenanceRequest',
        ]);


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

        $technicians = User::whereIn(
            'role',
            [
                'ENGINEER',
                'TECHNICIAN',
            ]
        )
            ->orderBy('username')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Maintenance Requests
        |--------------------------------------------------------------------------
        |
        | Hanya APPROVED.
        |
        | Request milik WO ini tetap ditampilkan.
        |
        */

        $maintenanceRequests = MaintenanceRequest::with('equipment')
            ->where('status', 'APPROVED')
            ->where(function ($query) use ($workOrder) {

                $query
                    ->whereDoesntHave('workOrder')
                    ->orWhereKey(
                        $workOrder->maintenance_request_id
                    );
            })
            ->latest()
            ->get();


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'work-orders.edit',
            compact(
                'workOrder',
                'equipment',
                'technicians',
                'maintenanceRequests'
            )
        );
    }


    /**
     * Update the specified work order.
     */
    public function update(
        Request $request,
        WorkOrder $workOrder
    ) {

        /*
        |--------------------------------------------------------------------------
        | Status tidak boleh diubah melalui edit biasa.
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'maintenance_request_id' => [
                'required',
                'exists:maintenance_requests,id',
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
        | Maintenance Request
        |--------------------------------------------------------------------------
        */

        $maintenanceRequest = MaintenanceRequest::with('equipment')
            ->findOrFail(
                $validated['maintenance_request_id']
            );


        /*
        |--------------------------------------------------------------------------
        | Maintenance Request harus APPROVED
        |--------------------------------------------------------------------------
        */

        if ($maintenanceRequest->status !== 'APPROVED') {

            return back()
                ->withInput()
                ->with(
                    'warning',
                    'Work Order hanya dapat menggunakan Maintenance Request yang sudah APPROVED.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Work Order
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Validate Technician
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['technician_id'])) {

            $technician = User::whereIn(
                'role',
                [
                    'ENGINEER',
                    'TECHNICIAN',
                ]
            )
                ->where(
                    'id',
                    $validated['technician_id']
                )
                ->first();

            if (!$technician) {

                return back()
                    ->withInput()
                    ->with(
                        'warning',
                        'User yang dipilih bukan Engineer atau Technician.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        |
        | equipment_id tidak diambil dari form.
        | Equipment selalu mengikuti Maintenance Request.
        |
        */

        $workOrder->update([

            'maintenance_request_id' =>
                $maintenanceRequest->id,

            'equipment_id' =>
                $maintenanceRequest->equipment_id,

            'technician_id' =>
                $validated['technician_id'] ?? null,

            'maintenance_type' =>
                $validated['maintenance_type'],

            'priority' =>
                $validated['priority'],

            'problem_description' =>
                $validated['problem_description'] ?? null,

            'root_cause' =>
                $validated['root_cause'] ?? null,

            'corrective_action' =>
                $validated['corrective_action'] ?? null,

            'planned_start' =>
                $validated['planned_start'] ?? null,

            'planned_end' =>
                $validated['planned_end'] ?? null,

            'completion_notes' =>
                $validated['completion_notes'] ?? null,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Jika WO masih OPEN dan Technician diisi
        |--------------------------------------------------------------------------
        */

        if (
            $workOrder->status === 'OPEN'
            && !empty($validated['technician_id'])
        ) {

            $workOrder->update([
                'status' => 'ASSIGNED',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'work-orders.show',
                $workOrder
            )
            ->with(
                'success',
                "Work Order {$workOrder->wo_number} berhasil diperbarui."
            );
    }


    /**
     * Generate Work Order Number.
     *
     * Format:
     *
     * WO-YYYYMMDD-0001
     */
    private function generateWoNumber(): string
    {
        $prefix = 'WO-' . now()->format('Ymd') . '-';


        /*
        |--------------------------------------------------------------------------
        | Get Last WO
        |--------------------------------------------------------------------------
        */

        $lastWorkOrder = WorkOrder::where(
            'wo_number',
            'like',
            $prefix . '%'
        )
            ->orderByDesc('wo_number')
            ->lockForUpdate()
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Sequence
        |--------------------------------------------------------------------------
        */

        if (!$lastWorkOrder) {

            $sequence = 1;

        } else {

            $lastSequence = (int) substr(
                $lastWorkOrder->wo_number,
                -4
            );

            $sequence = $lastSequence + 1;
        }


        /*
        |--------------------------------------------------------------------------
        | Return
        |--------------------------------------------------------------------------
        */

        return $prefix . str_pad(
            $sequence,
            4,
            '0',
            STR_PAD_LEFT
        );
    }


    /**
     * Assign Work Order to Technician.
     */
    public function assign(
        Request $request,
        WorkOrder $workOrder
    ) {

        /*
        |--------------------------------------------------------------------------
        | Status Validation
        |--------------------------------------------------------------------------
        */

        if ($workOrder->status !== 'OPEN') {

            return back()->with(
                'warning',
                'Work Order hanya dapat di-assign ketika status masih OPEN.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'technician_id' => [
                'required',
                'exists:users,id',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Validate Technician Role
        |--------------------------------------------------------------------------
        */

        $technician = User::whereIn(
            'role',
            [
                'ENGINEER',
                'TECHNICIAN',
            ]
        )
            ->where(
                'id',
                $validated['technician_id']
            )
            ->first();


        if (!$technician) {

            return back()->with(
                'warning',
                'User yang dipilih bukan Engineer atau Technician.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Assign
        |--------------------------------------------------------------------------
        */

        $workOrder->update([

            'technician_id' =>
                $technician->id,

            'status' =>
                'ASSIGNED',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            "Work Order {$workOrder->wo_number} berhasil di-assign kepada {$technician->username}."
        );
    }


    /**
     * Start Work Order.
     */
    public function start(WorkOrder $workOrder)
    {
        /*
        |--------------------------------------------------------------------------
        | Status Validation
        |--------------------------------------------------------------------------
        */

        if ($workOrder->status !== 'ASSIGNED') {

            return back()->with(
                'warning',
                'Work Order hanya dapat dimulai ketika status ASSIGNED.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Technician Validation
        |--------------------------------------------------------------------------
        */

        if (!$workOrder->technician_id) {

            return back()->with(
                'warning',
                'Work Order belum memiliki Technician/Engineer.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Start Transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($workOrder) {

            /*
            |--------------------------------------------------------------------------
            | Update Work Order
            |--------------------------------------------------------------------------
            */

            $workOrder->update([

                'status' =>
                    'IN_PROGRESS',

                'actual_start' =>
                    now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Equipment menjadi MAINTENANCE
            |--------------------------------------------------------------------------
            */

            $workOrder
                ->equipment()
                ->update([
                    'status' => 'MAINTENANCE',
                ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            "Work Order {$workOrder->wo_number} mulai dikerjakan."
        );
    }


    /**
     * Hold Work Order.
     */
    public function hold(WorkOrder $workOrder)
    {
        if ($workOrder->status !== 'IN_PROGRESS') {

            return back()->with(
                'warning',
                'Work Order hanya dapat ditunda ketika sedang IN PROGRESS.'
            );
        }


        $workOrder->update([
            'status' => 'ON_HOLD',
        ]);


        return back()->with(
            'success',
            "Work Order {$workOrder->wo_number} berhasil ditunda."
        );
    }


    /**
     * Resume Work Order.
     */
    public function resume(WorkOrder $workOrder)
    {
        if ($workOrder->status !== 'ON_HOLD') {

            return back()->with(
                'warning',
                'Work Order hanya dapat dilanjutkan ketika status ON HOLD.'
            );
        }


        $workOrder->update([
            'status' => 'IN_PROGRESS',
        ]);


        return back()->with(
            'success',
            "Work Order {$workOrder->wo_number} dilanjutkan kembali."
        );
    }


    /**
     * Complete Work Order.
     */
    public function complete(
        Request $request,
        WorkOrder $workOrder
    ) {

        /*
        |--------------------------------------------------------------------------
        | Status Validation
        |--------------------------------------------------------------------------
        */

        if ($workOrder->status !== 'IN_PROGRESS') {

            return back()->with(
                'warning',
                'Work Order hanya dapat diselesaikan ketika status IN PROGRESS.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'completion_notes' => [
                'required',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Complete Transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $workOrder,
            $validated
        ) {

            /*
            |--------------------------------------------------------------------------
            | Complete Work Order
            |--------------------------------------------------------------------------
            */

            $workOrder->update([

                'status' =>
                    'COMPLETED',

                'actual_end' =>
                    now(),

                'completion_notes' =>
                    $validated['completion_notes'],
            ]);


            /*
            |--------------------------------------------------------------------------
            | Equipment kembali ACTIVE
            |--------------------------------------------------------------------------
            */

            $workOrder
                ->equipment()
                ->update([
                    'status' => 'ACTIVE',
                ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            "Work Order {$workOrder->wo_number} berhasil diselesaikan."
        );
    }


    /**
     * Cancel Work Order.
     */
    public function cancel(WorkOrder $workOrder)
    {
        /*
        |--------------------------------------------------------------------------
        | Allowed Status
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            $workOrder->status,
            [
                'OPEN',
                'ASSIGNED',
                'ON_HOLD',
            ],
            true
        )) {

            return back()->with(
                'warning',
                'Work Order dengan status tersebut tidak dapat dibatalkan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Cancel
        |--------------------------------------------------------------------------
        */

        $workOrder->update([
            'status' => 'CANCELLED',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            "Work Order {$workOrder->wo_number} berhasil dibatalkan."
        );
    }
}