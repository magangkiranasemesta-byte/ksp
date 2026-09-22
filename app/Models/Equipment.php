<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
}
