<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PHC extends Model
{
    use HasFactory;

    protected $table = "phcs";

    protected $fillable = [
        'project_id',
        'ho_marketings_id',
        'ho_engineering_id',
        'created_by',
        'notes',
        'start_date',
        'target_finish_date',
        'client_name',
        'client_mobile',
        'client_reps_office_address',
        'client_site_address',
        'client_site_representatives',
        'site_phone_number',
        'status',
        'pic_engineering_id',
        'pic_marketing_id',
        'costing_by_marketing',
        'boq',
        'retention',
        'warranty',
        'penalty',
        'scope_of_work_approval',
        'organization_chart',
        'project_schedule',
        'component_list',
        'progress_claim_report',
        'component_approval_list',
        'design_approval_draw',
        'shop_draw',
        'fat_sat_forms',
        'daily_weekly_progress_report',
        'do_packing_list',
        'site_testing_commissioning_report',
        'as_build_draw',
        'manual_documentation',
        'accomplishment_report',
        'client_document_requirements',
        'job_safety_analysis',
        'risk_assessment',
        'tool_list',
        'handover_date'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }


    public function hoMarketing()
    {
        return $this->belongsTo(User::class, 'ho_marketings_id');
    }

    public function hoEngineering()
    {
        return $this->belongsTo(User::class, 'ho_engineering_id');
    }

    public function picEngineering()
    {
        return $this->belongsTo(User::class, 'pic_engineering_id');
    }

    public function picMarketing()
    {
        return $this->belongsTo(User::class, 'pic_marketing_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvals()
    {
        return $this->hasMany(PhcApproval::class, 'phc_id'); // foreign key yang benar
    }

    // Tambahkan ini untuk memastikan ho_engineering_id bisa diisi
    protected $attributes = [
        'ho_engineering_id' => null
    ];

    // Tambahkan event observer
    protected static function booted()
    {
        static::updating(function ($phc) {
            if ($phc->isDirty('ho_engineering_id')) {
                \Log::info("HO Engineering Updated", [
                    'phc_id' => $phc->id,
                    'old_value' => $phc->getOriginal('ho_engineering_id'),
                    'new_value' => $phc->ho_engineering_id
                ]);
            }
        });
    }

}
