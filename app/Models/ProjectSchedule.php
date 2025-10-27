<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSchedule extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable = ['project_id', 'name'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'pn_number');
    }

    public function tasks()
    {
        return $this->hasMany(ProjectScheduleTask::class);
    }
}
