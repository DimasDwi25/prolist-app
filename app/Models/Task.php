<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

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
