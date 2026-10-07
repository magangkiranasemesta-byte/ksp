<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreventiveMaintenanceLog extends Model
{
    protected $fillable = [
        'preventive_maintenance_id',
        'work_order_id',
        'performed_by',
        'due_date',
        'performed_at',
        'next_maintenance_date',
        'notes',
    ];

    protected $casts = [
        'due_date'              => 'date',
        'performed_at'          => 'datetime',
        'next_maintenance_date' => 'date',
    ];

    public function preventiveMaintenance(): BelongsTo
    {
        return $this->belongsTo(PreventiveMaintenance::class, 'preventive_maintenance_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
