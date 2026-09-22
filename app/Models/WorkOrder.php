<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WorkOrder extends Model
{
    use LogsActivity;

    protected $table = 'work_orders';

    protected $fillable = [
        'maintenance_request_id',
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
}