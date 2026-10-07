<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Jadwal Preventive Maintenance (berulang).
 *
 * Kolom `status` (enum lama: scheduled|in_progress|completed|overdue) dipakai:
 *   scheduled   = menunggu jadwal
 *   in_progress = Work Order sedang berjalan
 * "Overdue" dan "Due soon" TIDAK disimpan; keduanya dihitung dari
 * next_maintenance_date, sehingga tidak butuh perubahan database.
 */
class PreventiveMaintenance extends Model
{
    use HasFactory, LogsActivity;

    public const DUE_SOON_DAYS = 7;

    public const FREQUENCIES = [
        'daily'   => 'Harian',
        'weekly'  => 'Mingguan',
        'monthly' => 'Bulanan',
        'yearly'  => 'Tahunan',
    ];

    protected $table = 'preventive_maintenances';

    protected $guarded = ['id'];

    protected $casts = [
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('preventive_maintenance')
            ->setDescriptionForEvent(fn (string $eventName) => "Jadwal Preventive Maintenance telah di-{$eventName}");
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'preventive_maintenance_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PreventiveMaintenanceLog::class, 'preventive_maintenance_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Due state (dihitung, tidak disimpan)
    |--------------------------------------------------------------------------
    */

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereDate('next_maintenance_date', '<', today());
    }

    public function scopeDueSoon(Builder $query): Builder
    {
        return $query
            ->whereDate('next_maintenance_date', '>=', today())
            ->whereDate('next_maintenance_date', '<=', today()->addDays(self::DUE_SOON_DAYS));
    }

    public function scopeSafe(Builder $query): Builder
    {
        return $query->whereDate('next_maintenance_date', '>', today()->addDays(self::DUE_SOON_DAYS));
    }

    /**
     * overdue | due_soon | safe
     */
    public function getDueStateAttribute(): string
    {
        if (! $this->next_maintenance_date) {
            return 'safe';
        }

        $due = $this->next_maintenance_date->copy()->startOfDay();

        if ($due->lt(today())) {
            return 'overdue';
        }

        return $due->lte(today()->addDays(self::DUE_SOON_DAYS)) ? 'due_soon' : 'safe';
    }

    /** Selisih hari ke jadwal (negatif = terlambat). */
    public function getDaysUntilDueAttribute(): ?int
    {
        return $this->next_maintenance_date
            ? (int) today()->diffInDays($this->next_maintenance_date->copy()->startOfDay(), false)
            : null;
    }

    /** Tanggal jadwal berikutnya dihitung dari $from. */
    public function calculateNextDate(\Carbon\CarbonInterface $from): \Carbon\Carbon
    {
        $from = \Carbon\Carbon::instance($from)->startOfDay();

        return match ($this->frequency) {
            'daily'   => $from->addDay(),
            'weekly'  => $from->addWeek(),
            'yearly'  => $from->addYear(),
            default   => $from->addMonth(),
        };
    }
}
