<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

/**
 * Siapa boleh melakukan apa pada Work Order.
 *
 * - Perencana (SUPERADMIN, ADMIN, SUPERVISOR, MANAGER): membuat, mengubah,
 *   menugaskan, dan membatalkan WO.
 * - Eksekutor (technician yang ditugaskan, atau SUPERADMIN):
 *   start, hold, resume, complete.
 */
class WorkOrderPolicy
{
    private const PLANNERS = ['SUPERADMIN', 'ADMIN', 'SUPERVISOR', 'MANAGER'];

    private function role(User $user): string
    {
        return strtoupper((string) $user->role);
    }

    private function isPlanner(User $user): bool
    {
        return $user->hasPermission('maintenance')
            && in_array($this->role($user), self::PLANNERS, true);
    }

    private function isExecutor(User $user, WorkOrder $workOrder): bool
    {
        if (! $user->hasPermission('maintenance')) {
            return false;
        }

        return $this->role($user) === 'SUPERADMIN'
            || ($workOrder->technician_id !== null
                && (int) $workOrder->technician_id === (int) $user->id);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('maintenance');
    }

    public function view(User $user, WorkOrder $workOrder): bool
    {
        if (! $user->hasPermission('maintenance')) {
            return false;
        }

        // Engineer hanya melihat WO yang ditugaskan kepadanya.
        return $this->role($user) !== 'ENGINEER'
            || (int) $workOrder->technician_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $this->isPlanner($user);
    }

    public function update(User $user, WorkOrder $workOrder): bool
    {
        return $this->isPlanner($user);
    }

    public function assign(User $user, WorkOrder $workOrder): bool
    {
        return $this->isPlanner($user);
    }

    public function cancel(User $user, WorkOrder $workOrder): bool
    {
        return $this->isPlanner($user);
    }

    public function start(User $user, WorkOrder $workOrder): bool
    {
        return $this->isExecutor($user, $workOrder);
    }

    public function hold(User $user, WorkOrder $workOrder): bool
    {
        return $this->isExecutor($user, $workOrder);
    }

    public function resume(User $user, WorkOrder $workOrder): bool
    {
        return $this->isExecutor($user, $workOrder);
    }

    public function complete(User $user, WorkOrder $workOrder): bool
    {
        return $this->isExecutor($user, $workOrder);
    }
}
