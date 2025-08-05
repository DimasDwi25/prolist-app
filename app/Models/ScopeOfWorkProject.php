<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScopeOfWorkProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope_of_work_id',
        'project_id',
        'description',
    ];

    // Relasi ke master scope of work
    public function scopeOfWork()
    {
        return $this->belongsTo(ScopeOfWork::class);
    }

    // Relasi ke project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'pn_number');
    }
}
