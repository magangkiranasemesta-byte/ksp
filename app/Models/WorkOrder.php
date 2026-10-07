<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WorkOrder extends Model
{
    use LogsActivity;

    protected $table = 'work_orders';

    protected $fillable = [
        'maintenance_request_id',
        'preventive_maintenance_id',
        'equipment_id',
        'technician_id',
        'wo_number',
        'maintenance_type',
        'priority',
        'problem_description',
        'root_cause',
        'corrective_action',
        'planned_start',
        'planned_end',
        'actual_start',
        'actual_end',
        'status',
        'completion_notes',
    ];

    protected $casts = [
        'planned_start' => 'datetime',
        'planned_end' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('work_order')
            ->setDescriptionForEvent(
                fn (string $eventName) =>
                    "Work Order telah di-{$eventName}"
            );
    }

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(
            MaintenanceRequest::class,
            'maintenance_request_id'
        );
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(
            Equipment::class,
            'equipment_id'
        );
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'technician_id'
        );
    }

    public function preventiveMaintenance(): BelongsTo
    {
        return $this->belongsTo(PreventiveMaintenance::class, 'preventive_maintenance_id');
    }

    public function sparepartUsages(): HasMany
    {
        return $this->hasMany(SparepartUsage::class, 'work_order_id');
    }

    /**
     * Format: WO-YYYYMMDD-0001. Panggil di dalam DB::transaction.
     * Kolom wo_number UNIQUE menjadi pengaman terakhir.
     */
    public static function generateNumber(): string
    {
        $prefix = 'WO-' . now()->format('Ymd') . '-';

        $last = static::where('wo_number', 'like', $prefix . '%')
            ->orderByDesc('wo_number')
            ->lockForUpdate()
            ->first();

        $sequence = $last ? ((int) substr($last->wo_number, -4)) + 1 : 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
