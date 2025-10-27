<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable = [
        'name', 'type_role'
    ];

    /**
     * The roles that belong to the role.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');  
    }
    
}
