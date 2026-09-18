<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MaintenanceEvidence extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Evidence ini milik satu ticket.
     */
    public function ticket()
    {
        return $this->belongsTo(
            MaintenanceTicket::class,
            'ticket_id'
        );
    }

    /**
     * User yang mengupload evidence.
     */
    public function uploader()
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }

    /**
     * URL gambar evidence.
     */
    public function getImageUrlAttribute()
    {
        return Storage::disk('public')->url(
            $this->image_path
        );
    }
}