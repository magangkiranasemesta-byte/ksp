<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;

/**
 * Siapa boleh melakukan apa pada Maintenance Request.
 *
 * Policy hanya menjawab "siapa". Validasi "kondisi/status" dilakukan
 * di controller (dengan row lock) supaya user mendapat pesan yang jelas.
 */
class MaintenanceRequestPolicy
{
    private function role(User $user): string
    {
        return strtoupper((string) $user->role);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('maintenance');
    }

    public function view(User $user, MaintenanceRequest $request): bool
    {
        if (! $user->hasPermission('maintenance')) {
            return false;
        }

        // Engineer hanya boleh melihat request miliknya sendiri.
        return $this->role($user) !== 'ENGINEER'
            || (int) $request->engineer_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('maintenance')
            && in_array($this->role($user), ['SUPERADMIN', 'ADMIN', 'ENGINEER'], true);
    }

    /**
     * Approve mengikuti tahap yang sedang berjalan:
     * PENDING_SUPERVISOR -> Supervisor, PENDING_MANAGER -> Manager.
     */
    public function approve(User $user, MaintenanceRequest $request): bool
    {
        if (! $user->hasPermission('maintenance')) {
            return false;
        }

        return match ($request->status) {
            'PENDING_SUPERVISOR' => in_array($this->role($user), ['SUPERADMIN', 'SUPERVISOR'], true),
            'PENDING_MANAGER'    => in_array($this->role($user), ['SUPERADMIN', 'MANAGER'], true),
            default              => false,
        };
    }

    public function reject(User $user, MaintenanceRequest $request): bool
    {
        return $this->approve($user, $request);
    }
}
