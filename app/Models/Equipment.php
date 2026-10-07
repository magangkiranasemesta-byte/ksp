<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Equipment extends Model
{
    use LogsActivity;

    protected $table = 'equipment';
    protected $fillable = ['equipment_code', 'name', 'location', 'description', 'status'];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('equipment')
            ->setDescriptionForEvent(fn(string $eventName) => "Data mesin/peralatan telah di-{$eventName}");
    }

    public function downtimes(): HasMany
    {
        return $this->hasMany(EquipmentDowntime::class, 'equipment_id');
    }
    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'equipment_id');
    }
    public function workOrders(): HasMany
    {
        return $this->hasMany(
            WorkOrder::class,
            'equipment_id'
        );
    }

    public function preventiveMaintenances(): HasMany
    {
        return $this->hasMany(PreventiveMaintenance::class, 'equipment_id');
    }

    /** Downtime yang sedang berjalan (maksimal satu per equipment). */
    public function ongoingDowntime(): HasOne
    {
        return $this->hasOne(EquipmentDowntime::class, 'equipment_id')
            ->where('status', 'ONGOING')
            ->latestOfMany();
    }

    /** Work Order yang sedang dikerjakan / ditunda. */
    public function currentWorkOrder(): HasOne
    {
        return $this->hasOne(WorkOrder::class, 'equipment_id')
            ->whereIn('status', ['IN_PROGRESS', 'ON_HOLD'])
            ->latestOfMany();
    }
}
