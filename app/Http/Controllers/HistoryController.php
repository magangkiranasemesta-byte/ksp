<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Display maintenance history.
     */
    public function index(Request $request)
    {
        $query = WorkOrder::with([
            'equipment',
            'technician',
            'maintenanceRequest',
        ])->whereIn('status', [
            'COMPLETED',
            'CANCELLED',
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
        | Filter Maintenance Type
        |--------------------------------------------------------------------------
        */

        if ($request->filled('maintenance_type')) {
            $query->where(
                'maintenance_type',
                $request->maintenance_type
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $histories = $query
            ->latest('actual_end')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalHistory = WorkOrder::whereIn(
            'status',
            [
                'COMPLETED',
                'CANCELLED',
            ]
        )->count();

        $completedHistory = WorkOrder::where(
            'status',
            'COMPLETED'
        )->count();

        $cancelledHistory = WorkOrder::where(
            'status',
            'CANCELLED'
        )->count();

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'history.index',
            compact(
                'histories',
                'totalHistory',
                'completedHistory',
                'cancelledHistory'
            )
        );
    }
}