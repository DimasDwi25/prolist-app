<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectWeekSchedule extends Model
{
    use HasFactory, ActivityLoggable;
    protected $fillable =
        [
            'project_schedule_task_id',
            'week_number',
            'week_start',
            'week_end',
            'bobot_plan',
            'bobot_actual'
        ];

    public function task()
    {
        return $this->belongsTo(ProjectScheduleTask::class, 'project_schedule_task_id');
    }

}
