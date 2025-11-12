<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterExpedition extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable = [
        'name',
        'alias_name',
        'description',
        'remark',
    ];
}
