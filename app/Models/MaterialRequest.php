<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory, ActivityLoggable;

    protected $table = 'material_requests';

    protected $fillable = [
        'pn_id',
        'material_number',
        'material_description',
        'material_created',
        'created_by',
        'target_date',
        'cancel_date',
        'complete_date',
        'material_status_id',
        'additional_material',
        'material_handover',
        'ho_date',
        'remark',
    ];

    // Relasi ke Project
    public function project()
    {
        return $this->belongsTo(Project::class, 'pn_id', 'pn_number');
    }

    // Relasi ke User (creator)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mrHandover()
    {
        return $this->belongsTo(User::class, 'material_handover');
    }

    public function materialStatus()
    {
        return $this->belongsTo(MasterStatusMr::class, 'material_status_id');
    }
}
