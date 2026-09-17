<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\MaintenanceTicket;
use App\Models\PreventiveMaintenance;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = strtoupper($user->role);

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */
        if ($role === 'SUPERADMIN') {

            // ==============================
            // STATISTIK UTAMA
            // ==============================

            $totalUsers = User::count();

            $totalEquipment = Equipment::count();

            $totalSpareparts = Sparepart::count();

            $totalTickets = MaintenanceTicket::count();

            $completedTickets = MaintenanceTicket::whereIn('status', [
                'resolved',
                'closed'
            ])->count();

            $activeTickets = MaintenanceTicket::whereIn('status', [
                'open',
                'in_progress',
                'pending_sparepart'
            ])->count();

            $urgentTickets = MaintenanceTicket::where('priority', 'urgent')
                ->whereNotIn('status', [
                    'resolved',
                    'closed',
                    'cancelled'
                ])
                ->count();

            // ==============================
            // COMPLETION RATE
            // ==============================

            $completionRate = $totalTickets > 0
                ? round(($completedTickets / $totalTickets) * 100)
                : 0;

            // ==============================
            // LOW STOCK
            // ==============================

            $lowStock = Sparepart::whereColumn(
                'stock',
                '<=',
                'min_stock'
            )->count();

            // ==============================
            // OVERDUE PREVENTIVE MAINTENANCE
            // ==============================

            $overdueMaintenance = PreventiveMaintenance::whereDate(
                'next_maintenance_date',
                '<',
                now()->toDateString()
            )->count();

            // ==============================
            // STATUS SUMMARY
            // ==============================

            $statusSummary = MaintenanceTicket::select(
                'status',
                DB::raw('COUNT(*) as total')
            )
                ->groupBy('status')
                ->pluck('total', 'status');

            // ==============================
            // TICKET 6 BULAN TERAKHIR
            // ==============================

            $monthlyTickets = collect();

            for ($i = 5; $i >= 0; $i--) {

                $date = now()->subMonths($i);

                $monthlyTickets->push([
                    'label' => $date->format('M Y'),

                    'total' => MaintenanceTicket::whereYear(
                        'created_at',
                        $date->year
                    )
                        ->whereMonth(
                            'created_at',
                            $date->month
                        )
                        ->count()
                ]);
            }

            // ==============================
            // TICKET TERBARU
            // ==============================

            $recentTickets = MaintenanceTicket::with([
                'device',
                'reporter',
                'technician'
            ])
                ->latest()
                ->take(8)
                ->get();

            // ==============================
            // KIRIM DATA KE VIEW
            // ==============================

            return view('dashboard.superadmin', compact(
                'totalUsers',
                'totalEquipment',
                'totalSpareparts',
                'totalTickets',
                'completedTickets',
                'activeTickets',
                'urgentTickets',
                'completionRate',
                'lowStock',
                'overdueMaintenance',
                'statusSummary',
                'monthlyTickets',
                'recentTickets'
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */
        if ($role === 'ADMIN') {

            $totalTickets = MaintenanceTicket::count();

            $pendingTickets = MaintenanceTicket::where(
                'status',
                'open'
            )->count();

            $inProgressTickets = MaintenanceTicket::whereIn('status', [
                'in_progress',
                'pending_sparepart'
            ])->count();

            $completedTickets = MaintenanceTicket::whereIn('status', [
                'resolved',
                'closed'
            ])->count();

            $lowStockSpareparts = Sparepart::whereColumn(
                'stock',
                '<=',
                'min_stock'
            )->get();

            $recentTickets = MaintenanceTicket::with([
                'device',
                'reporter',
                'technician'
            ])
                ->latest()
                ->take(6)
                ->get();

            return view('dashboard.admin', compact(
                'totalTickets',
                'pendingTickets',
                'inProgressTickets',
                'completedTickets',
                'lowStockSpareparts',
                'recentTickets'
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | ENGINEER / TEKNISI
        |--------------------------------------------------------------------------
        */
        if (in_array($role, ['ENGINEER', 'TEKNISI'])) {

            // Ambil tiket yang ditugaskan kepada engineer yang sedang login
            $myTicketsQuery = MaintenanceTicket::where(
                'assigned_to',
                $user->id
            );

            // ==============================
            // JUMLAH SEMUA TIKET SAYA
            // ==============================

            $myTicketsCount = (clone $myTicketsQuery)->count();

            // ==============================
            // TIKET OPEN
            // ==============================

            $myPendingTickets = (clone $myTicketsQuery)
                ->where('status', 'open')
                ->count();

            // ==============================
            // TIKET AKTIF
            // ==============================

            $myActiveTickets = (clone $myTicketsQuery)
                ->whereIn('status', [
                    'in_progress',
                    'pending_sparepart'
                ])
                ->count();

            // ==============================
            // TIKET SELESAI
            // ==============================

            $myDoneTickets = (clone $myTicketsQuery)
                ->whereIn('status', [
                    'resolved',
                    'closed'
                ])
                ->count();

            // ==============================
            // DAFTAR TIKET ENGINEER
            // ==============================

            $myTickets = (clone $myTicketsQuery)
                ->with('device')
                ->latest()
                ->take(6)
                ->get();

            // ==============================
            // KIRIM DATA KE ENGINEER VIEW
            // ==============================

            return view('dashboard.engineer', compact(
                'myTickets',
                'myTicketsCount',
                'myPendingTickets',
                'myActiveTickets',
                'myDoneTickets'
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | SUPERVISOR
        |--------------------------------------------------------------------------
        */
        if ($role === 'SUPERVISOR') {

            $totalTickets = MaintenanceTicket::count();

            $pendingTickets = MaintenanceTicket::where(
                'status',
                'open'
            )->count();

            $inProgressTickets = MaintenanceTicket::whereIn(
                'status',
                [
                    'in_progress',
                    'pending_sparepart'
                ]
            )->count();

            $completedTickets = MaintenanceTicket::whereIn(
                'status',
                [
                    'resolved',
                    'closed'
                ]
            )->count();

            return view('dashboard.supervisor', compact(
                'totalTickets',
                'pendingTickets',
                'inProgressTickets',
                'completedTickets'
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | MANAGER
        |--------------------------------------------------------------------------
        */
        if ($role === 'MANAGER') {

            $totalTickets = MaintenanceTicket::count();

            $completedTickets = MaintenanceTicket::whereIn(
                'status',
                [
                    'resolved',
                    'closed'
                ]
            )->count();

            $completionRate = $totalTickets > 0
                ? round(($completedTickets / $totalTickets) * 100)
                : 0;

            return view('dashboard.manager', [
                'totalTickets' => $totalTickets,
                'completedTickets' => $completedTickets,
                'completionRate' => $completionRate,
                'totalEquipment' => Equipment::count(),
                'totalSpareparts' => Sparepart::count(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */
        $myReportedCount = MaintenanceTicket::where(
            'reported_by',
            $user->id
        )->count();

        $myActiveCount = MaintenanceTicket::where(
            'reported_by',
            $user->id
        )
            ->whereIn('status', [
                'open',
                'in_progress',
                'pending_sparepart'
            ])
            ->count();

        $myCompletedCount = MaintenanceTicket::where(
            'reported_by',
            $user->id
        )
            ->whereIn('status', [
                'resolved',
                'closed'
            ])
            ->count();

        return view('dashboard.user', compact(
            'myReportedCount',
            'myActiveCount',
            'myCompletedCount'
        ));
    }
}