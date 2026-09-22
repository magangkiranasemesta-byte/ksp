<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use Notifiable, LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'permissions',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Attributes
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'permissions' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('user')
            ->dontLogIfAttributesChangedOnly(['remember_token'])
            ->logExcept(['password'])
            ->setDescriptionForEvent(
                fn (string $eventName) => "Akun user telah di-{$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Permission
    |--------------------------------------------------------------------------
    */

    public function hasPermission(string $permission): bool
    {
        if (strtoupper($this->role) === 'SUPERADMIN') {
            return true;
        }

        return is_array($this->permissions)
            && in_array(
                $permission,
                $this->permissions,
                true
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Maintenance Relationship
    |--------------------------------------------------------------------------
    */

    public function maintenanceRequests()
    {
        return $this->hasMany(
            MaintenanceRequest::class,
            'engineer_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Approval History Relationship
    |--------------------------------------------------------------------------
    */

    public function approvalHistory()
    {
        return $this->hasMany(
            ApprovalHistory::class
        );
    }

    public function workOrders()
    {
        return $this->hasMany(
            WorkOrder::class,
            'technician_id'
        );
    }
}
