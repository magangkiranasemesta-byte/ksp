<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SparepartUsage extends Model
{
    protected $fillable = [
        'sparepart_id',
        'user_id',
        'maintenance_ticket_id',
        'quantity',
        'notes',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(
            Sparepart::class,
            'sparepart_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(
            MaintenanceTicket::class,
            'maintenance_ticket_id'
        );
    }
}