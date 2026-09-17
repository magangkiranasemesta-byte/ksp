<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Sparepart extends Model
{
    use LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'category',
        'stock',
        'min_stock',
        'unit',
        'price',
        'description',
    ];

    protected $guarded = [
        'id',
    ];

    /**
     * Cek apakah stok sparepart sudah rendah.
     */
    public function isLowStock()
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Relasi sparepart dengan maintenance ticket.
     */
    public function tickets()
    {
        return $this->belongsToMany(
            MaintenanceTicket::class,
            'ticket_spareparts'
        )
        ->withPivot(
            'quantity',
            'unit_price',
            'total_price'
        )
        ->withTimestamps();
    }

    /**
     * Riwayat pemakaian sparepart.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(
            SparepartUsage::class,
            'sparepart_id'
        );
    }

    /**
     * Activity Log.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('sparepart')
            ->setDescriptionForEvent(
                fn (string $eventName) =>
                    "Stok/Data sparepart telah di-{$eventName}"
            );
    }
}