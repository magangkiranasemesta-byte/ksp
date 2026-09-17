<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PreventiveMaintenance extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('preventive_maintenance')
            ->setDescriptionForEvent(fn(string $eventName) => "Jadwal Preventive Maintenance telah di-{$eventName}");
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class); // Sesuaikan dengan Model Equipment Anda
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
