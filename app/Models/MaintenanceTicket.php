<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MaintenanceTicket extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function logs()
    {
        return $this->hasMany(
            MaintenanceLog::class,
            'ticket_id'
        );
    }

    public function statusHistories()
    {
        return $this->hasMany(
            TicketStatusHistory::class,
            'ticket_id'
        );
    }

    public function spareparts()
    {
        return $this->belongsToMany(
            Sparepart::class,
            'ticket_spareparts'
        )
        ->withPivot(
            'quantity',
            'unit_price',
            'total_price'
        )
        ->withTimestamps();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('maintenance_ticket')
            ->setDescriptionForEvent(
                fn (string $eventName) =>
                    "Ticket maintenance telah di-{$eventName}"
            );
    }
}