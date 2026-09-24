<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Http\Request;

class MaintenanceRequestController extends Controller
{
    /**
     * Display a listing of maintenance requests.
     */
    public function index(Request $request)
    {
        $query = MaintenanceRequest::with([
            'equipment',
            'engineer',
            'workOrder',
        ])->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'description',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'priority',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'status',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'equipment',
                    function ($equipment) use ($search) {

                        $equipment
                            ->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'equipment_code',
                                'like',
                                "%{$search}%"
                            );
                    }
                )

                ->orWhereHas(
                    'engineer',
                    function ($engineer) use ($search) {

                        $engineer->where(
                            'username',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        // Filter priority
        if ($request->filled('priority')) {
            $query->where(
                'priority',
                $request->priority
            );
        }

        $maintenanceRequests = $query
            ->paginate(10)
            ->withQueryString();

        // Statistik
        $total = MaintenanceRequest::count();

        $pendingSupervisor = MaintenanceRequest::where(
            'status',
            'PENDING_SUPERVISOR'
        )->count();

        $pendingManager = MaintenanceRequest::where(
            'status',
            'PENDING_MANAGER'
        )->count();

        $approved = MaintenanceRequest::where(
            'status',
            'APPROVED'
        )->count();

        $rejected = MaintenanceRequest::where(
            'status',
            'REJECTED'
        )->count();

        return view(
            'maintenance.index',
            compact(
                'maintenanceRequests',
                'total',
                'pendingSupervisor',
                'pendingManager',
                'approved',
                'rejected'
            )
        );
    }


    /**
     * Show create form.
     */
    public function create()
    {
        $equipment = Equipment::orderBy('name')
            ->get();

        $engineers = User::whereIn('role', [
            'ENGINEER',
            'TECHNICIAN',
        ])
            ->orderBy('username')
            ->get();

        return view(
            'maintenance.create',
            compact(
                'equipment',
                'engineers'
            )
        );
    }


    /**
     * Store maintenance request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'equipment_id' => [
                'required',
                'exists:equipment,id',
            ],

            'engineer_id' => [
                'required',
                'exists:users,id',
            ],

            'description' => [
                'required',
                'string',
                'min:10',
            ],

            'priority' => [
                'required',
                'in:LOW,MEDIUM,HIGH,CRITICAL',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Pastikan user yang dipilih memang Engineer / Technician
        |--------------------------------------------------------------------------
        */

        $engineer = User::whereIn('role', [
            'ENGINEER',
            'TECHNICIAN',
        ])
            ->where(
                'id',
                $validated['engineer_id']
            )
            ->first();

        if (!$engineer) {
            return back()
                ->withInput()
                ->with(
                    'warning',
                    'User yang dipilih bukan Engineer atau Technician.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Status awal
        |--------------------------------------------------------------------------
        */

        $validated['status'] = 'PENDING_SUPERVISOR';

        MaintenanceRequest::create(
            $validated
        );

        return redirect()
            ->route('maintenance.index')
            ->with(
                'success',
                'Maintenance Request berhasil dibuat dan menunggu approval Supervisor.'
            );
    }


    /**
     * Display maintenance request.
     */
    public function show(
        MaintenanceRequest $maintenanceRequest
    ) {
        $maintenanceRequest->load([
            'equipment',
            'engineer',
            'approvals',
            'workOrder',
        ]);

        return view(
            'maintenance.show',
            compact(
                'maintenanceRequest'
            )
        );
    }


    /**
     * Show edit form.
     */
    public function edit(
        MaintenanceRequest $maintenanceRequest
    ) {
        /*
        |--------------------------------------------------------------------------
        | Request yang sudah menjadi Work Order
        | tidak boleh diedit sembarangan.
        |--------------------------------------------------------------------------
        */

        if ($maintenanceRequest->workOrder) {
            return redirect()
                ->route(
                    'maintenance.show',
                    $maintenanceRequest
                )
                ->with(
                    'warning',
                    'Maintenance Request yang sudah memiliki Work Order tidak dapat diedit.'
                );
        }

        $equipment = Equipment::orderBy('name')
            ->get();

        $engineers = User::whereIn('role', [
            'ENGINEER',
            'TECHNICIAN',
        ])
            ->orderBy('username')
            ->get();

        return view(
            'maintenance.edit',
            compact(
                'maintenanceRequest',
                'equipment',
                'engineers'
            )
        );
    }


    /**
     * Update maintenance request.
     */
    public function update(
        Request $request,
        MaintenanceRequest $maintenanceRequest
    ) {
        if ($maintenanceRequest->workOrder) {
            return back()->with(
                'warning',
                'Maintenance Request yang sudah memiliki Work Order tidak dapat diperbarui.'
            );
        }

        $validated = $request->validate([

            'equipment_id' => [
                'required',
                'exists:equipment,id',
            ],

            'engineer_id' => [
                'required',
                'exists:users,id',
            ],

            'description' => [
                'required',
                'string',
                'min:10',
            ],

            'priority' => [
                'required',
                'in:LOW,MEDIUM,HIGH,CRITICAL',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Jangan izinkan edit biasa mengubah status approval.
        |--------------------------------------------------------------------------
        */

        $maintenanceRequest->update(
            $validated
        );

        return redirect()
            ->route(
                'maintenance.show',
                $maintenanceRequest
            )
            ->with(
                'success',
                'Maintenance Request berhasil diperbarui.'
            );
    }


    /**
     * Delete maintenance request.
     */
    public function destroy(
        MaintenanceRequest $maintenanceRequest
    ) {
        if ($maintenanceRequest->workOrder) {
            return back()->with(
                'warning',
                'Maintenance Request yang sudah memiliki Work Order tidak dapat dihapus.'
            );
        }

        $maintenanceRequest->delete();

        return redirect()
            ->route('maintenance.index')
            ->with(
                'success',
                'Maintenance Request berhasil dihapus.'
            );
    }
}