<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentDowntime;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceTicket;
use App\Models\PreventiveMaintenance;
use App\Models\Sparepart;
use App\Models\User;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

/**
 * Data dashboard per peran.
 *
 * Prinsip:
 *  1. Setiap peran punya fokus berbeda (lihat superadmin(), admin(), supervisor(), manager()).
 *  2. Widget HANYA diisi bila user punya hak akses modulnya. View menampilkan
 *     widget bila kuncinya ada di array, sehingga data yang tidak berhak
 *     tidak pernah di-query maupun dikirim ke view.
 *  3. Semua angka berasal dari database.
 */
class DashboardService
{
    public const WO_STATUSES = ['OPEN', 'ASSIGNED', 'IN_PROGRESS', 'ON_HOLD', 'COMPLETED', 'CANCELLED'];

    public const WO_STATUS_COLORS = [
        'OPEN'        => '#94a3b8',
        'ASSIGNED'    => '#3b82f6',
        'IN_PROGRESS' => '#6366f1',
        'ON_HOLD'     => '#f59e0b',
        'COMPLETED'   => '#10b981',
        'CANCELLED'   => '#f43f5e',
    ];

    /**
     * Fitur -> permission. Satu tempat untuk disesuaikan ketika permission
     * dipisah lebih halus (Fase 5), misalnya 'downtime' => 'downtime'.
     */
    private const PERMISSIONS = [
        'workflow'   => 'maintenance',   // Maintenance Request, Work Order, Preventive
        'downtime'   => 'tickets',
        'tickets'    => 'tickets',
        'spareparts' => 'spareparts',
        'equipment'  => 'equipment',
        'activity'   => 'activity_logs',
        'users'      => 'users',
        'history'    => 'history',
    ];

    private const MODULE_LABELS = [
        'maintenance'   => 'Maintenance & Work Order',
        'tickets'       => 'Ticket & Downtime',
        'spareparts'    => 'Sparepart',
        'equipment'     => 'Equipment',
        'history'       => 'History',
        'activity_logs' => 'Activity Log',
        'users'         => 'User',
    ];

    private const TECHNICIAN_ROLES = ['ENGINEER', 'TECHNICIAN'];

    /*
    |--------------------------------------------------------------------------
    | Entry point
    |--------------------------------------------------------------------------
    */

    public function forRole(User $user, string $role): array
    {
        return match ($role) {
            'SUPERADMIN' => $this->superadmin($user),
            'ADMIN'      => $this->admin($user),
            'SUPERVISOR' => $this->supervisor($user),
            'MANAGER'    => $this->manager($user),
            default      => [],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | SUPERADMIN: pusat kendali & audit sistem
    |--------------------------------------------------------------------------
    */

    private function superadmin(User $user): array
    {
        $d = ['header' => $this->header(
            $user,
            'Superadmin',
            'bg-slate-900',
            'Pusat Kendali Sistem',
            'Seluruh modul, pengguna, dan jejak aktivitas dalam satu tampilan.',
            [
                ['Kelola User', 'users', 'users.index'],
                ['Activity Log', 'activity', 'activity-logs.index'],
                ['Work Order', 'workflow', 'work-orders.index'],
            ]
        )];

        if ($this->allows($user, 'workflow')) {
            $counts = $this->statusCounts(WorkOrder::query());
            $total  = (int) $counts->sum();

            $d['kpi'] = $this->woKpi($user, $counts, $total);

            $d['attention'] = [
                ['Menunggu approval', MaintenanceRequest::whereIn('status', ['PENDING_SUPERVISOR', 'PENDING_MANAGER'])->count(), 'text-amber-600', route('maintenance.index', ['search' => 'PENDING'])],
                ['WO belum di-assign', $counts['OPEN'], 'text-slate-700', route('work-orders.index', ['status' => 'OPEN'])],
                ['PM overdue', PreventiveMaintenance::overdue()->count(), 'text-rose-600', route('maintenance.preventive.index', ['state' => 'overdue'])],
                ['Stok menipis', $this->allows($user, 'spareparts') ? $this->lowStockCount() : 0, 'text-orange-600', $this->allows($user, 'spareparts') ? route('spareparts.index') : null],
            ];

            $d['chart']            = $this->chart($counts, $total);
            $d['recentWorkOrders'] = $this->recentWorkOrders(6);
            [$d['upcomingPm'], $d['pmSummary']] = $this->preventive(6);
        }

        $this->attachCommon($d, $user, ['downtime' => 5, 'activity' => 8, 'equipment' => true, 'tickets' => true, 'lowStock' => 5]);

        if ($this->allows($user, 'users')) {
            $d['usersByRole'] = User::select('role', DB::raw('COUNT(*) as total'))->groupBy('role')->orderBy('role')->pluck('total', 'role');
        }

        return $d;
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN: operasional harian (dispatcher)
    |--------------------------------------------------------------------------
    */

    private function admin(User $user): array
    {
        $d = ['header' => $this->header(
            $user,
            'Admin',
            'bg-blue-700',
            'Operasional Harian',
            'Antrean pekerjaan yang perlu ditindaklanjuti: buat WO, assign teknisi, dan jadwal preventive.',
            [
                ['Buat Work Order', 'workflow', 'work-orders.create'],
                ['Preventive', 'workflow', 'maintenance.preventive.index'],
                ['Equipment', 'equipment', 'equipment.index'],
            ]
        )];

        if ($this->allows($user, 'workflow')) {
            $counts   = $this->statusCounts(WorkOrder::query());
            $readyAll = $this->readyForWoQuery();

            $d['kpi'] = [
                $this->card('Perlu di-assign', $counts['OPEN'], 'WO berstatus OPEN', 'text-slate-800', route('work-orders.index', ['status' => 'OPEN'])),
                $this->card('Siap dibuat WO', (clone $readyAll)->count(), 'Request APPROVED', 'text-blue-600', null),
                $this->card('In Progress', $counts['IN_PROGRESS'], 'Sedang dikerjakan', 'text-indigo-600', route('work-orders.index', ['status' => 'IN_PROGRESS'])),
            ];

            $d['readyForWo']    = (clone $readyAll)->with(['equipment', 'engineer'])->oldest()->limit(6)->get();
            $d['unassignedWo']  = WorkOrder::with('equipment')->where('status', 'OPEN')->latest()->limit(6)->get();
            [$d['upcomingPm'], $d['pmSummary']] = $this->preventive(6);
            $d['recentWorkOrders'] = $this->recentWorkOrders(5);
            $d['chart']            = $this->chart($counts, (int) $counts->sum());
        }

        $this->attachCommon($d, $user, ['downtime' => 4, 'activity' => 6, 'lowStock' => 5, 'downtimeKpi' => true]);

        return $d;
    }

    /*
    |--------------------------------------------------------------------------
    | SUPERVISOR: approval tahap 1 & pemantauan tim
    |--------------------------------------------------------------------------
    */

    private function supervisor(User $user): array
    {
        $d = ['header' => $this->header(
            $user,
            'Supervisor',
            'bg-indigo-700',
            'Approval dan Pemantauan Tim',
            'Request yang menunggu keputusan Anda, serta beban kerja teknisi.',
            [
                ['Review Approval', 'workflow', 'maintenance.index', ['status' => 'PENDING_SUPERVISOR']],
                ['Work Order', 'workflow', 'work-orders.index'],
            ]
        )];

        if ($this->allows($user, 'workflow')) {
            $counts  = $this->statusCounts(WorkOrder::query());
            $pending = MaintenanceRequest::where('status', 'PENDING_SUPERVISOR');

            $d['kpi'] = [
                $this->card('Menunggu approval Anda', (clone $pending)->count(), 'Tahap Supervisor', 'text-amber-600', route('maintenance.index', ['status' => 'PENDING_SUPERVISOR'])),
                $this->card('WO aktif', $counts['IN_PROGRESS'] + $counts['ON_HOLD'], 'In progress dan on hold', 'text-indigo-600', null),
                $this->card('On Hold', $counts['ON_HOLD'], 'Perlu ditindaklanjuti', 'text-amber-600', route('work-orders.index', ['status' => 'ON_HOLD'])),
                $this->card('PM overdue', PreventiveMaintenance::overdue()->count(), 'Jadwal terlambat', 'text-rose-600', route('maintenance.preventive.index', ['state' => 'overdue'])),
            ];

            $d['approvalQueueTitle'] = 'Menunggu Approval Supervisor';
            $d['approvalQueue']      = $pending->with(['equipment', 'engineer'])->oldest()->limit(6)->get();
            $d['workload']           = $this->workload();
            $d['activeWorkOrders']   = WorkOrder::with(['equipment', 'technician'])->whereIn('status', ['IN_PROGRESS', 'ON_HOLD'])->latest('updated_at')->limit(6)->get();
            $d['chart']              = $this->chart($counts, (int) $counts->sum());
            [$d['upcomingPm'], $d['pmSummary']] = $this->preventive(5);
        }

        $this->attachCommon($d, $user, ['downtime' => 4, 'activity' => 6]);

        return $d;
    }

    /*
    |--------------------------------------------------------------------------
    | MANAGER: eksekutif (kinerja & approval tahap akhir)
    |--------------------------------------------------------------------------
    */

    private function manager(User $user): array
    {
        $d = ['header' => $this->header(
            $user,
            'Manager',
            'bg-emerald-700',
            'Ringkasan Kinerja Maintenance',
            'Tingkat penyelesaian, waktu pengerjaan, downtime, dan approval tahap akhir.',
            [
                ['Review Approval', 'workflow', 'maintenance.index', ['status' => 'PENDING_MANAGER']],
                ['History & Laporan', 'history', 'history.index'],
            ]
        )];

        if ($this->allows($user, 'workflow')) {
            $counts = $this->statusCounts(WorkOrder::query());
            $total  = (int) $counts->sum();
            $rate   = $total > 0 ? (int) round($counts['COMPLETED'] / $total * 100) : 0;

            $monthStart = now()->startOfMonth();

            $d['kpi'] = [
                $this->card('Completion rate', $rate . '%', "{$counts['COMPLETED']} dari {$total} WO", 'text-emerald-600', null),
                $this->card('Selesai bulan ini', WorkOrder::where('status', 'COMPLETED')->where('actual_end', '>=', $monthStart)->count(), now()->translatedFormat('F Y'), 'text-slate-800', null),
                $this->card('Rata-rata pengerjaan', $this->averageCompletion(), 'Dari 100 WO terakhir', 'text-indigo-600', null),
            ];

            if ($this->allows($user, 'downtime')) {
                $d['kpi'][] = $this->card('Downtime bulan ini', $this->downtimeThisMonth(), 'Total semua equipment', 'text-rose-600', route('downtime.index'));
            }

            $d['approvalQueueTitle'] = 'Menunggu Approval Manager';
            $d['approvalQueue']      = MaintenanceRequest::with(['equipment', 'engineer'])->where('status', 'PENDING_MANAGER')->oldest()->limit(6)->get();
            $d['trend']              = $this->trend();
            $d['chart']              = $this->chart($counts, $total);
            [$d['upcomingPm'], $d['pmSummary']] = $this->preventive(5);
        }

        $this->attachCommon($d, $user, ['downtime' => 4, 'equipment' => true, 'activity' => 6]);

        return $d;
    }

    /*
    |--------------------------------------------------------------------------
    | ENGINEER / TECHNICIAN: pekerjaan milik sendiri
    |--------------------------------------------------------------------------
    */

    public function engineer(User $user): array
    {
        $counts = $this->statusCounts(WorkOrder::where('technician_id', $user->id));

        $myWorkOrders = WorkOrder::with('equipment')
            ->where('technician_id', $user->id)
            ->whereIn('status', ['ASSIGNED', 'IN_PROGRESS', 'ON_HOLD'])
            ->orderByRaw("CASE status WHEN 'IN_PROGRESS' THEN 0 WHEN 'ON_HOLD' THEN 1 ELSE 2 END")
            ->latest()
            ->limit(6)
            ->get();

        $requestCounts = MaintenanceRequest::where('engineer_id', $user->id)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $requests = [
            'pending'  => (int) ($requestCounts['PENDING_SUPERVISOR'] ?? 0) + (int) ($requestCounts['PENDING_MANAGER'] ?? 0),
            'approved' => (int) ($requestCounts['APPROVED'] ?? 0),
            'rejected' => (int) ($requestCounts['REJECTED'] ?? 0),
        ];

        return [
            'stats' => [
                'assigned'    => $counts['ASSIGNED'],
                'in_progress' => $counts['IN_PROGRESS'],
                'on_hold'     => $counts['ON_HOLD'],
                'completed'   => $counts['COMPLETED'],
            ],
            'requests'     => $requests,
            'myWorkOrders' => $myWorkOrders,
            'myRequests'   => MaintenanceRequest::with(['equipment', 'workOrder'])->where('engineer_id', $user->id)->latest()->limit(5)->get(),
            'myPreventives' => PreventiveMaintenance::with('equipment')->where('assigned_to', $user->id)->orderBy('next_maintenance_date')->limit(5)->get(),
            'myActivities' => Activity::with('causer')
                ->where('causer_id', $user->id)
                ->where('causer_type', $user->getMorphClass())
                ->latest()
                ->limit(6)
                ->get(),
            'myTickets' => MaintenanceTicket::where('assigned_to', $user->id)
                ->whereIn('status', ['open', 'in_progress', 'pending_sparepart'])
                ->count(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Widget bersama (diisi hanya jika berhak)
    |--------------------------------------------------------------------------
    */

    /**
     * @param array $want downtime(int limit) | downtimeKpi(bool) | activity(int limit)
     *                    | equipment(bool) | tickets(bool) | lowStock(int limit)
     */
    private function attachCommon(array &$d, User $user, array $want): void
    {
        if (isset($want['downtime']) && $this->allows($user, 'downtime')) {
            $d['downtimes'] = EquipmentDowntime::with('equipment')
                ->orderByRaw("CASE WHEN status = 'ONGOING' THEN 0 ELSE 1 END")
                ->latest('started_at')
                ->limit($want['downtime'])
                ->get();

            if (! empty($want['downtimeKpi']) && isset($d['kpi'])) {
                $d['kpi'][] = $this->card(
                    'Downtime',
                    EquipmentDowntime::where('status', 'ONGOING')->count(),
                    'Equipment sedang down',
                    'text-rose-600',
                    route('downtime.index')
                );
            }
        }

        if (isset($want['activity'])) {
            $activity = $this->activities($user, $want['activity']);

            if ($activity !== null) {
                $d['activities']    = $activity['items'];
                $d['activityTitle'] = $activity['title'];
            }
        }

        if (! empty($want['equipment']) && $this->allows($user, 'equipment')) {
            $d['equipmentSummary'] = Equipment::select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
        }

        if (! empty($want['tickets']) && $this->allows($user, 'tickets')) {
            $d['ticketSummary'] = [
                'open'   => MaintenanceTicket::where('status', 'open')->count(),
                'active' => MaintenanceTicket::whereIn('status', ['in_progress', 'pending_sparepart'])->count(),
                'done'   => MaintenanceTicket::whereIn('status', ['resolved', 'closed'])->count(),
            ];
        }

        if (isset($want['lowStock']) && $this->allows($user, 'spareparts')) {
            $d['lowStock'] = Sparepart::whereColumn('stock', '<=', 'min_stock')
                ->orderBy('stock')
                ->limit($want['lowStock'])
                ->get();
        }
    }

    /**
     * Aktivitas:
     *  - berhak activity_logs -> semua log
     *  - berhak maintenance   -> hanya log WO, request, preventive, downtime
     *  - lainnya              -> tidak tampil
     */
    private function activities(User $user, int $limit): ?array
    {
        if ($this->allows($user, 'activity')) {
            return [
                'title' => 'Aktivitas Terbaru',
                'items' => Activity::with('causer')->latest()->limit($limit)->get(),
            ];
        }

        if ($this->allows($user, 'workflow')) {
            return [
                'title' => 'Aktivitas Maintenance',
                'items' => Activity::with('causer')
                    ->whereIn('subject_type', [
                        (new WorkOrder)->getMorphClass(),
                        (new MaintenanceRequest)->getMorphClass(),
                        (new PreventiveMaintenance)->getMorphClass(),
                        (new EquipmentDowntime)->getMorphClass(),
                    ])
                    ->latest()
                    ->limit($limit)
                    ->get(),
            ];
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Builders kecil
    |--------------------------------------------------------------------------
    */

    private function allows(User $user, string $feature): bool
    {
        return $user->hasPermission(self::PERMISSIONS[$feature]);
    }

    private function card(string $label, int|string $value, string $hint, string $color, ?string $href): array
    {
        return compact('label', 'value', 'hint', 'color', 'href');
    }

    /** Empat kartu standar berbasis Work Order. */
    private function woKpi(User $user, Collection $counts, int $total): array
    {
        $rate = $total > 0 ? (int) round($counts['COMPLETED'] / $total * 100) : 0;

        $cards = [
            $this->card('Total WO', $total, 'Semua Work Order', 'text-slate-800', route('work-orders.index')),
            $this->card('In Progress', $counts['IN_PROGRESS'], 'Sedang dikerjakan', 'text-indigo-600', route('work-orders.index', ['status' => 'IN_PROGRESS'])),
            $this->card('Completed', $counts['COMPLETED'], "{$rate}% dari total", 'text-emerald-600', route('work-orders.index', ['status' => 'COMPLETED'])),
        ];

        if ($this->allows($user, 'downtime')) {
            $active  = EquipmentDowntime::where('status', 'ONGOING')->count();
            $cards[] = $this->card('Downtime', $active, 'Equipment sedang down', $active > 0 ? 'text-rose-600' : 'text-slate-800', route('downtime.index'));
        }

        return $cards;
    }

    private function header(User $user, string $label, string $bg, string $title, string $subtitle, array $actions): array
    {
        $links = [];

        foreach ($actions as $action) {
            [$text, $feature, $route] = $action;
            $params = $action[3] ?? [];

            if ($this->allows($user, $feature)) {
                $links[] = ['label' => $text, 'href' => route($route, $params)];
            }
        }

        return [
            'role_label' => $label,
            'bg'         => $bg,
            'title'      => $title,
            'subtitle'   => $subtitle,
            'modules'    => $this->accessibleModules($user),
            'actions'    => $links,
        ];
    }

    private function accessibleModules(User $user): string
    {
        if (strtoupper((string) $user->role) === 'SUPERADMIN') {
            return 'Semua modul';
        }

        $labels = collect(self::MODULE_LABELS)
            ->filter(fn ($label, $permission) => $user->hasPermission($permission))
            ->values();

        return $labels->isEmpty() ? 'Dashboard' : $labels->implode(', ');
    }

    private function statusCounts($query): Collection
    {
        $raw = $query->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(self::WO_STATUSES)
            ->mapWithKeys(fn (string $status) => [$status => (int) ($raw[$status] ?? 0)]);
    }

    private function readyForWoQuery()
    {
        return MaintenanceRequest::where('status', 'APPROVED')->whereDoesntHave('workOrder');
    }

    private function recentWorkOrders(int $limit)
    {
        return WorkOrder::with(['equipment', 'technician'])->latest()->limit($limit)->get();
    }

    /** @return array{0: \Illuminate\Support\Collection, 1: array} */
    private function preventive(int $limit): array
    {
        return [
            PreventiveMaintenance::with(['equipment', 'technician'])->orderBy('next_maintenance_date')->limit($limit)->get(),
            [
                'overdue'  => PreventiveMaintenance::overdue()->count(),
                'due_soon' => PreventiveMaintenance::dueSoon()->count(),
                'safe'     => PreventiveMaintenance::safe()->count(),
            ],
        ];
    }

    private function lowStockCount(): int
    {
        return Sparepart::whereColumn('stock', '<=', 'min_stock')->count();
    }

    /** Beban kerja per technician (WO aktif). */
    private function workload(): Collection
    {
        $rows = WorkOrder::whereIn('status', ['ASSIGNED', 'IN_PROGRESS', 'ON_HOLD'])
            ->whereNotNull('technician_id')
            ->select('technician_id', 'status', DB::raw('COUNT(*) as total'))
            ->groupBy('technician_id', 'status')
            ->get()
            ->groupBy('technician_id');

        return User::whereIn('role', self::TECHNICIAN_ROLES)
            ->orderBy('username')
            ->get(['id', 'username'])
            ->map(function (User $tech) use ($rows) {
                $mine = ($rows[$tech->id] ?? collect())->pluck('total', 'status');

                return [
                    'name'        => $tech->username,
                    'assigned'    => (int) ($mine['ASSIGNED'] ?? 0),
                    'in_progress' => (int) ($mine['IN_PROGRESS'] ?? 0),
                    'on_hold'     => (int) ($mine['ON_HOLD'] ?? 0),
                ];
            })
            ->sortByDesc(fn ($r) => $r['assigned'] + $r['in_progress'] + $r['on_hold'])
            ->values()
            ->take(8);
    }

    /** WO dibuat vs selesai, 6 bulan terakhir. */
    private function trend(): array
    {
        $start = now()->startOfMonth()->subMonths(5);

        $created = WorkOrder::where('created_at', '>=', $start)->pluck('created_at')
            ->map(fn ($v) => Carbon::parse($v)->format('Y-m'));

        $done = WorkOrder::where('status', 'COMPLETED')->where('actual_end', '>=', $start)->pluck('actual_end')
            ->map(fn ($v) => Carbon::parse($v)->format('Y-m'));

        $months = [];

        for ($i = 0; $i < 6; $i++) {
            $month = $start->copy()->addMonths($i);
            $key   = $month->format('Y-m');

            $months[] = [
                'label'     => $month->translatedFormat('M Y'),
                'created'   => $created->filter(fn ($k) => $k === $key)->count(),
                'completed' => $done->filter(fn ($k) => $k === $key)->count(),
            ];
        }

        return [
            'months' => $months,
            'max'    => max(1, collect($months)->max(fn ($m) => max($m['created'], $m['completed']))),
        ];
    }

    private function averageCompletion(): string
    {
        $rows = WorkOrder::where('status', 'COMPLETED')
            ->whereNotNull('actual_start')
            ->whereNotNull('actual_end')
            ->latest('actual_end')
            ->limit(100)
            ->get(['actual_start', 'actual_end']);

        if ($rows->isEmpty()) {
            return '-';
        }

        $minutes = (int) round($rows->avg(fn ($r) => (int) $r->actual_start->diffInMinutes($r->actual_end)));

        return $this->formatMinutes($minutes);
    }

    private function downtimeThisMonth(): string
    {
        $monthStart = now()->startOfMonth();

        $minutes = EquipmentDowntime::where(function ($q) use ($monthStart) {
                $q->whereNull('ended_at')->orWhere('ended_at', '>=', $monthStart);
            })
            ->get(['started_at', 'ended_at'])
            ->sum(function ($d) use ($monthStart) {
                $from = $d->started_at->greaterThan($monthStart) ? $d->started_at : $monthStart;

                return max(0, (int) $from->diffInMinutes($d->ended_at ?? now()));
            });

        return $minutes > 0 ? $this->formatMinutes((int) $minutes) : '0 menit';
    }

    private function formatMinutes(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} menit";
        }

        if ($minutes < 1440) {
            return round($minutes / 60, 1) . ' jam';
        }

        return round($minutes / 1440, 1) . ' hari';
    }

    /** Donut chart (CSS conic-gradient, tanpa library tambahan). */
    private function chart(Collection $counts, int $total): array
    {
        $segments = [];
        $stops    = [];
        $cursor   = 0.0;

        foreach ($counts as $status => $count) {
            $percent = $total > 0 ? $count / $total * 100 : 0.0;
            $color   = self::WO_STATUS_COLORS[$status];

            if ($count > 0) {
                $stops[] = sprintf('%s %s%% %s%%', $color, number_format($cursor, 2, '.', ''), number_format($cursor + $percent, 2, '.', ''));
            }

            $cursor += $percent;

            $segments[] = [
                'status'  => $status,
                'label'   => str_replace('_', ' ', $status),
                'count'   => $count,
                'percent' => (int) round($percent),
                'color'   => $color,
            ];
        }

        return [
            'total'    => $total,
            'segments' => $segments,
            'gradient' => $stops
                ? 'conic-gradient(' . implode(', ', $stops) . ')'
                : 'conic-gradient(#e2e8f0 0% 100%)',
        ];
    }
}
