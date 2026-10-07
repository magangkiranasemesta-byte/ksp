<?php

namespace App\Policies;

use App\Models\PreventiveMaintenance;
use App\Models\User;

class PreventiveMaintenancePolicy
{
    private const PLANNERS = ['SUPERADMIN', 'ADMIN', 'SUPERVISOR', 'MANAGER'];

    private function isPlanner(User $user): bool
    {
        return $user->hasPermission('maintenance')
            && in_array(strtoupper((string) $user->role), self::PLANNERS, true);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('maintenance');
    }

    public function view(User $user, PreventiveMaintenance $preventive): bool
    {
        return $user->hasPermission('maintenance');
    }

    public function create(User $user): bool
    {
        return $this->isPlanner($user);
    }

    public function update(User $user, PreventiveMaintenance $preventive): bool
    {
        return $this->isPlanner($user);
    }

    public function generateWorkOrder(User $user, PreventiveMaintenance $preventive): bool
    {
        return $this->isPlanner($user);
    }

    /** Penyelesaian manual (tanpa Work Order): perencana atau technician yang ditugaskan. */
    public function complete(User $user, PreventiveMaintenance $preventive): bool
    {
        if (! $user->hasPermission('maintenance')) {
            return false;
        }

        return $this->isPlanner($user)
            || ($preventive->assigned_to !== null && (int) $preventive->assigned_to === (int) $user->id);
    }
}
