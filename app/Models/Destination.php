<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable = [
        'destination',
        'address',
        'alias',
    ];
}
