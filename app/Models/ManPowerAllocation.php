<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManPowerAllocation extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable = [
        'project_id', 'user_id', 'role_id'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'pn_number');
    }

    /**
     * Relasi ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
