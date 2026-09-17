<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MaintenanceRequest extends Model
{
    use LogsActivity;

    protected $table = 'maintenance_requests';
    protected $fillable = ['equipment_id', 'engineer_id', 'description', 'priority', 'status'];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('maintenance_request')
            ->setDescriptionForEvent(fn(string $eventName) => "Permintaan pemeliharaan telah di-{$eventName}");
    }

    public function equipment(): BelongsTo { return $this->belongsTo(Equipment::class, 'equipment_id'); }
    public function engineer(): BelongsTo { return $this->belongsTo(User::class, 'engineer_id'); }
    public function approvals(): HasMany { return $this->hasMany(ApprovalHistory::class, 'maintenance_id'); }
}
