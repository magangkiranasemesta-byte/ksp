<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceTicket;
use App\Models\User;
use App\Services\DashboardService;

/**
 * Dashboard berbasis peran.
 *
 *  Setiap peran punya view sendiri dengan fokus berbeda:
 *    SUPERADMIN -> dashboard.superadmin  (kendali sistem & audit)
 *    ADMIN      -> dashboard.admin       (antrean operasional)
 *    SUPERVISOR -> dashboard.supervisor  (approval tahap 1 & beban tim)
 *    MANAGER    -> dashboard.manager     (kinerja & approval tahap akhir)
 *    ENGINEER   -> dashboard.engineer    (pekerjaan milik sendiri)
 *    lainnya    -> dashboard.user        (ringkasan ticket pribadi)
 *
 *  Widget di dalam tiap view hanya tampil bila user berhak (lihat DashboardService).
 */
class DashboardController extends Controller
{
    private const MANAGEMENT_ROLES = ['SUPERADMIN', 'ADMIN', 'SUPERVISOR', 'MANAGER'];

    private const TECHNICIAN_ROLES = ['ENGINEER', 'TEKNISI', 'TECHNICIAN'];

    public function __construct(private DashboardService $dashboard)
    {
    }

    public function index()
    {
        $user = auth()->user();
        $role = $this->resolveRole($user);

        if (in_array($role, self::MANAGEMENT_ROLES, true)) {
            return view('dashboard.' . strtolower($role), [
                'role' => $role,
                'd'    => $this->dashboard->forRole($user, $role),
            ]);
        }

        if (in_array($role, self::TECHNICIAN_ROLES, true)) {
            return view('dashboard.engineer', $this->dashboard->engineer($user));
        }

        return $this->userDashboard($user);
    }

    /**
     * Peran yang dipakai untuk memilih tampilan.
     * (Titik penyambung untuk simulasi peran Superadmin pada Fase 5.)
     */
    private function resolveRole(User $user): string
    {
        return strtoupper((string) $user->role);
    }

    private function userDashboard(User $user)
    {
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
