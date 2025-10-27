<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScopeOfWorkProject extends Model
{
    use HasFactory, ActivityLoggable;

    protected $table = 'scope_of_work_projects';

    protected $fillable = [
        'project_id',
        'work_details',
        'pic',
        'target_finish_date',
        'start_date',
        'finish_date',
    ];

    /**
     * Relasi ke Project.
     * Karena foreign key mengacu ke `projects.pn_number`,
     * maka local key = pn_number.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'pn_number');
    }

    /**
     * Relasi ke User sebagai PIC.
     */
    public function picUser()
    {
        return $this->belongsTo(User::class, 'pic', 'id');
    }

    // tambahkan accessor
    protected $appends = ['pic_name'];

    public function getPicNameAttribute()
    {
        return $this->picUser?->name;
    }
}
