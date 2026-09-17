<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalHistory extends Model
{
    protected $table = 'approval_history';
    public $timestamps = false;
    protected $fillable = ['maintenance_id', 'user_id', 'role', 'action', 'note', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function maintenance() { return $this->belongsTo(MaintenanceRequest::class, 'maintenance_id'); }
}
