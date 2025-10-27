<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable =
        [
            'task_name',
            'description'
        ];

    public function scheduleTasks()
    {
        return $this->hasMany(ProjectScheduleTask::class);
    }

}
