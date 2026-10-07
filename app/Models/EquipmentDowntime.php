<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EquipmentDowntime extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'equipment_id',
        'maintenance_request_id',
        'started_at',
        'ended_at',
        'reason',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('downtime')
            ->setDescriptionForEvent(fn (string $eventName) => "Downtime equipment telah di-{$eventName}");
    }

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'maintenance_request_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDurationAttribute()
    {
        if (!$this->started_at) {
            return '-';
        }

        $end = $this->ended_at ?? now();

        $minutes = $this->started_at->diffInMinutes($end);

        $days = intdiv($minutes, 1440);
        $hours = intdiv($minutes % 1440, 60);
        $remainingMinutes = $minutes % 60;

        $result = [];

        if ($days > 0) {
            $result[] = $days . ' hari';
        }

        if ($hours > 0) {
            $result[] = $hours . ' jam';
        }

        if ($remainingMinutes > 0 || empty($result)) {
            $result[] = $remainingMinutes . ' menit';
        }

        return implode(' ', $result);
    }
}