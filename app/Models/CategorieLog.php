<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieLog extends Model
{
    use HasFactory, ActivityLoggable;

    protected $table = 'categorie_logs';
    protected $fillable = [
        'name'
    ];

    public function logs()
    {
        return $this->hasMany(Log::class, 'categorie_log_id');
    }

}
