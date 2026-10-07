<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\PreventiveMaintenance;
use App\Models\PreventiveMaintenanceLog;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;

/**
 * Logika bisnis Preventive Maintenance yang dipakai bersama oleh
 * PreventiveMaintenanceController dan WorkOrderController.
 */
class PreventiveMaintenanceService
{
    /** Status WO yang dianggap "masih berjalan" untuk sebuah jadwal PM. */
    public const OPEN_WO_STATUSES = ['OPEN', 'ASSIGNED', 'IN_PROGRESS', 'ON_HOLD'];

    public function hasOpenWorkOrder(PreventiveMaintenance $pm): bool
    {
        return WorkOrder::where('preventive_maintenance_id', $pm->id)
            ->whereIn('status', self::OPEN_WO_STATUSES)
            ->exists();
    }

    /**
     * Buat Work Order PREVENTIVE dari jadwal PM.
     *
     * @return array{0: WorkOrder|null, 1: string|null} [workOrder, errorMessage]
     */
    public function generateWorkOrder(PreventiveMaintenance $preventive, User $by): array
    {
        return DB::transaction(function () use ($preventive, $by) {
            $pm = PreventiveMaintenance::lockForUpdate()->findOrFail($preventive->id);

            $equipment = Equipment::find($pm->equipment_id);

            if (! $equipment) {
                return [null, 'Equipment untuk jadwal ini tidak ditemukan.'];
            }

            if ($equipment->status === 'INACTIVE') {
                return [null, 'Equipment berstatus INACTIVE, Work Order tidak dapat dibuat.'];
            }

            if ($this->hasOpenWorkOrder($pm)) {
                return [null, 'Jadwal ini sudah memiliki Work Order yang masih berjalan.'];
            }

            $technician = $pm->assigned_to
                ? User::whereIn('role', ['ENGINEER', 'TECHNICIAN'])->find($pm->assigned_to)
                : null;

            $workOrder = WorkOrder::create([
                'wo_number'                => WorkOrder::generateNumber(),
                'maintenance_request_id'   => null,
                'preventive_maintenance_id'=> $pm->id,
                'equipment_id'             => $pm->equipment_id,
                'technician_id'            => $technician?->id,
                'maintenance_type'         => 'PREVENTIVE',
                'priority'                 => 'MEDIUM',
                'problem_description'      => trim($pm->title . ($pm->notes ? "\n" . $pm->notes : '')),
                'planned_start'            => $pm->next_maintenance_date?->copy()->startOfDay(),
                'status'                   => $technician ? 'ASSIGNED' : 'OPEN',
            ]);

            $pm->forceFill(['status' => 'in_progress'])->saveQuietly();

            activity('preventive_maintenance')
                ->performedOn($pm)
                ->causedBy($by)
                ->event('generate_wo')
                ->withProperties(['work_order' => $workOrder->wo_number])
                ->log("Work Order {$workOrder->wo_number} dibuat dari jadwal \"{$pm->title}\"");

            return [$workOrder, null];
        });
    }

    /**
     * Tandai satu siklus PM selesai: catat riwayat, geser jadwal berikutnya.
     * Jadwal berikutnya dihitung dari tanggal pelaksanaan.
     */
    public function complete(
        PreventiveMaintenance $preventive,
        ?User $by,
        ?WorkOrder $workOrder = null,
        ?string $notes = null
    ): PreventiveMaintenanceLog {
        return DB::transaction(function () use ($preventive, $by, $workOrder, $notes) {
            $pm = PreventiveMaintenance::lockForUpdate()->findOrFail($preventive->id);

            $performedAt = $workOrder?->actual_end ?? now();
            $dueDate     = $pm->next_maintenance_date;
            $nextDate    = $pm->calculateNextDate($performedAt);

            $pm->forceFill([
                'last_maintenance_date' => $performedAt->toDateString(),
                'next_maintenance_date' => $nextDate->toDateString(),
                'status'                => 'scheduled',
            ])->saveQuietly();

            $log = PreventiveMaintenanceLog::create([
                'preventive_maintenance_id' => $pm->id,
                'work_order_id'             => $workOrder?->id,
                'performed_by'              => $by?->id,
                'due_date'                  => $dueDate?->toDateString(),
                'performed_at'              => $performedAt,
                'next_maintenance_date'     => $nextDate->toDateString(),
                'notes'                     => $notes,
            ]);

            $activity = activity('preventive_maintenance')
                ->performedOn($pm)
                ->event('complete')
                ->withProperties([
                    'work_order' => $workOrder?->wo_number,
                    'next_maintenance_date' => $nextDate->toDateString(),
                ]);

            if ($by) {
                $activity->causedBy($by);
            }

            $activity->log("Preventive Maintenance \"{$pm->title}\" selesai, jadwal berikutnya {$nextDate->format('d/m/Y')}");

            return $log;
        });
    }

    /** WO PM dibatalkan -> jadwal kembali menunggu (tanggal tidak digeser). */
    public function releaseAfterCancel(PreventiveMaintenance $preventive): void
    {
        $pm = PreventiveMaintenance::find($preventive->id);

        if ($pm && $pm->status === 'in_progress' && ! $this->hasOpenWorkOrder($pm)) {
            $pm->forceFill(['status' => 'scheduled'])->saveQuietly();
        }
    }
}
