<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatusHistory extends Model
{
    use HasFactory;

    // Nama tabel di database (jika tidak mengikuti penamaan jamak Laravel)
    protected $table = 'ticket_status_histories';

    // Kolom yang boleh diisi secara mass assignment
    protected $fillable = [
        'ticket_id',     // Foreign key ke tabel tickets / maintenance_tickets
        'user_id',       // Foreign key ke tabel users (siapa yang mengubah status)
        'status',        // Status ticket (contoh: OPEN, IN_PROGRESS, CLOSED)
        'notes',         // Catatan perubahan status (opsional)
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke model Ticket / MaintenanceTicket
     */
    public function ticket()
    {
        return $this->belongsTo(MaintenanceTicket::class, 'ticket_id');
    }

    /**
     * Relasi ke model User (Pengubah status)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}