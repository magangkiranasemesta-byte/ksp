<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Ticket extends Model
{
    
    use HasFactory, LogsActivity;

    protected $guarded = [];

    // Relasi ke User (Pelapor)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Mencatat semua perubahan kolom
            ->logOnlyDirty() // Hanya catat data yang mengalami perubahan
            ->useLogName('ticket') // Nama grup log
            ->setDescriptionForEvent(fn(string $eventName) => "Tiket telah di-{$eventName}");
    }
    
}