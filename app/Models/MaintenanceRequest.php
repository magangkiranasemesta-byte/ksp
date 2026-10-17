<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MaintenanceRequest extends Model
{
    use LogsActivity;

    /** Status yang tampil di halaman Maintenance History. */
    public const HISTORY_STATUSES = ['REJECTED', 'APPROVED', 'IN_PROGRESS', 'COMPLETED'];

    protected $table = 'maintenance_requests';
    protected $fillable = ['equipment_id', 'engineer_id', 'description', 'priority', 'status'];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('maintenance_request')
            ->setDescriptionForEvent(fn(string $eventName) => "Permintaan pemeliharaan telah di-{$eventName}");
    }

    public function equipment(): BelongsTo { return $this->belongsTo(Equipment::class, 'equipment_id'); }
    public function engineer(): BelongsTo { return $this->belongsTo(User::class, 'engineer_id'); }
    public function approvals(): HasMany { return $this->hasMany(ApprovalHistory::class, 'maintenance_id'); }
    public function workOrder()
    {
        return $this->hasOne(
            WorkOrder::class,
            'maintenance_request_id'
        );
    }

    /**
     * Basis data Maintenance History (satu-satunya sumber aturan "data apa yang
     * boleh tampil/diekspor"). Dipakai oleh list, export PDF, dan export Excel.
     */
    public function scopeForHistory(Builder $query): Builder
    {
        return $query->whereIn('status', self::HISTORY_STATUSES);
    }

    /**
     * Filter tanggal dibuat + status yang dipakai halaman history.
     */
    public function scopeFilterHistory(Builder $query, ?string $startDate = null, ?string $endDate = null, ?string $status = null): Builder
    {
        if (filled($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if (filled($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if (filled($status) && strtoupper($status) !== 'ALL') {
            $query->where('status', strtoupper($status));
        }

        return $query;
    }
}
