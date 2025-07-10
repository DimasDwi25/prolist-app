<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'categorie_log_id',
        'users_id',
        'logs',
        'tgl_logs',
        'status',
        'closing_date',
        'closing_users',
        'response_by',
        'need_response',
        'project_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function category()
    {
        return $this->belongsTo(CategorieLog::class, 'categorie_log_id');
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closing_users');
    }

    public function responseUser()
    {
        return $this->belongsTo(User::class, 'response_by');
    }


    protected $casts = [
        'tgl_logs' => 'datetime',
        'closing_date' => 'datetime',
    ];

}
