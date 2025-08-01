<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectScheduleTask extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'project_schedule_id',
            'task_id',
            'quantity',
            'unit',
            'bobot',
            'plan_start',
            'plan_finish',
            'actual_start',
            'actual_finish',
            'order'
        ];

    /**
     * Relasi ke Schedule
     */
    public function schedule()
    {
        return $this->belongsTo(ProjectSchedule::class, 'project_schedule_id');
    }

    /**
     * Relasi ke Master Task
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function weeks()
    {
        return $this->hasMany(ProjectWeekSchedule::class, 'project_schedule_task_id');
    }

    public function progress_weeks()
    {
        return $this->hasMany(ProjectWeekSchedule::class, 'project_schedule_task_id');
    }

    public function weekSchedules()
    {
        return $this->hasMany(\App\Models\ProjectWeekSchedule::class, 'project_schedule_task_id');
    }



    /**
     * Generate weeks dinamis
     */
    public function generateWeeks()
    {
        // Tentukan tanggal awal
        $startDate = $this->actual_start ?? $this->plan_start;
        if (!$startDate)
            return;

        // Tentukan tanggal akhir
        $endDate = $this->actual_finish ?? now();

        $start = Carbon::parse($startDate)->startOfWeek(Carbon::MONDAY);
        $end = Carbon::parse($endDate)->endOfWeek(Carbon::SUNDAY);

        $weekNumber = 1;

        // Kalau belum ada week → generate minimal W1
        if ($this->weeks()->count() === 0) {
            $this->weeks()->create([
                'week_number' => 1,
                'week_start' => $start,
                'week_end' => $start->copy()->endOfWeek(Carbon::SUNDAY),
                'bobot_plan' => 0,
                'bobot_actual' => 0,
            ]);
        }

        // Jika actual_finish sudah ada → generate sampai finish
        if ($this->actual_finish) {
            $lastWeek = $this->weeks()->orderByDesc('week_number')->first();
            $weekNumber = $lastWeek ? $lastWeek->week_number + 1 : 1;
            $start = $lastWeek ? Carbon::parse($lastWeek->week_start)->addWeek() : $start;

            while ($start <= $end) {
                $this->weeks()->firstOrCreate([
                    'week_number' => $weekNumber
                ], [
                    'week_start' => $start,
                    'week_end' => $start->copy()->endOfWeek(Carbon::SUNDAY),
                    'bobot_plan' => 0,
                    'bobot_actual' => 0,
                ]);
                $start->addWeek();
                $weekNumber++;
            }
        }
    }

    // protected static function booted()
    // {
    //     static::saved(function ($task) {
    //         // Generate minggu hanya jika actual_start diisi dan belum ada week sama sekali
    //         if ($task->actual_start && $task->weeks()->count() === 0) {
    //             $task->generateWeeks();
    //         }

    //         // Jika actual_finish diisi belakangan → extend minggu
    //         if ($task->actual_finish) {
    //             $task->generateWeeks();
    //         }
    //     });
    // }

}
