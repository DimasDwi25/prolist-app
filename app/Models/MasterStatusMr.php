<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterStatusMr extends Model
{
    use HasFactory, ActivityLoggable;

    protected $table = 'master_status_mrs';

    protected $fillable = [
        'name',
        'description',
    ];
}
