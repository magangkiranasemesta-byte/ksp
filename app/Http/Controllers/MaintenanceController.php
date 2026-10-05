<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Exports\MaintenanceHistoryExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * =========================================================================
     * HISTORY
     * =========================================================================
     */

    public function history(Request $request)
    {
        $query = MaintenanceRequest::with(
            'equipment',
            'engineer'
        )
            ->whereIn(
                'status',
                [
                    'REJECTED',
                    'APPROVED',
                    'IN_PROGRESS',
                    'COMPLETED'
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | Filter tanggal mulai
        |--------------------------------------------------------------------------
        */

        if ($request->filled('start_date')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->start_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter tanggal akhir
        |--------------------------------------------------------------------------
        */

        if ($request->filled('end_date')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->end_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter status
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('status')
            && $request->status !== 'ALL'
        ) {

            $query->where(
                'status',
                strtoupper($request->status)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $history = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'history.index',
            compact('history')
        );
    }

    /**
     * =========================================================================
     * EXPORT PDF
     * =========================================================================
     */

    public function exportPdf(Request $request)
    {
        $query = MaintenanceRequest::with(
            'equipment',
            'engineer'
        )
            ->whereIn(
                'status',
                [
                    'REJECTED',
                    'APPROVED',
                    'IN_PROGRESS',
                    'COMPLETED'
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | Filter tanggal mulai
        |--------------------------------------------------------------------------
        */

        if ($request->filled('start_date')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->start_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter tanggal akhir
        |--------------------------------------------------------------------------
        */

        if ($request->filled('end_date')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->end_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter status
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('status')
            && $request->status !== 'ALL'
        ) {

            $query->where(
                'status',
                strtoupper($request->status)
            );
        }

        $history = $query
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Data informasi laporan
        |--------------------------------------------------------------------------
        */

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $status = $request->status ?? 'ALL';

        $pdf = Pdf::loadView(
            'reports.maintenance-history',
            compact(
                'history',
                'startDate',
                'endDate',
                'status'
            )
        );

        // Landscape A4
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'maintenance-history-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    /**
     * =========================================================================
     * EXPORT EXCEL
     * =========================================================================
     */

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new MaintenanceHistoryExport(
                $request->start_date,
                $request->end_date,
                $request->status
            ),
            'maintenance-history-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }
}