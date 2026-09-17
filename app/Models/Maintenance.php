<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk menentukan nama tabel baru
    protected $table = 'preventive_maintenances';

    protected $fillable = [
        'equipment_id',
        'title',
        'frequency',
        'last_maintenance_date',
        'next_maintenance_date',
        'status',
        'notes',
        'assigned_to',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
