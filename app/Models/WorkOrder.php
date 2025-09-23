<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'wo_date',
        'wo_number_in_project',
        'wo_kode_no',
        'total_mandays_eng',
        'total_mandays_elect',
        'add_work',
        'status',
        'approved_by',
        'start_working_date',
        'end_working_date',
        'wo_count',
        'client_approved',
        'created_by',
        'accepted_by',
        'approved_by'
    ];

    /**
     * Relasi ke Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'pn_number');
    }

    /**
     * Relasi ke PIC (banyak)
     */
    public function pics()
    {
        return $this->hasMany(WorkOrderPic::class);
    }

    /**
     * Relasi ke Descriptions (banyak)
     */
    public function descriptions()
    {
        return $this->hasMany(WorkOrderDescription::class);
    }

    // Relasi Log (berdasarkan project_id)
    public function logs()
    {
        return $this->hasMany(Log::class, 'project_id', 'pn_number');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
