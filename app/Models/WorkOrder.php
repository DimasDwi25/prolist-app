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
        'pic1',
        'pic2',
        'pic3',
        'pic4',
        'pic5',
        'role_pic_1',
        'role_pic_2',
        'role_pic_3',
        'role_pic_4',
        'role_pic_5',
        'total_mandays_eng',
        'total_mandays_elect',
        'add_work',
        'work_description',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function pic1()
    {
        return $this->belongsTo(User::class, 'pic1');
    }

    public function pic2()
    {
        return $this->belongsTo(User::class, 'pic2');
    }

    public function pic3()
    {
        return $this->belongsTo(User::class, 'pic3');
    }

    public function pic4()
    {
        return $this->belongsTo(User::class, 'pic4');
    }

    public function pic5()
    {
        return $this->belongsTo(User::class, 'pic5');
    }

    public function rolePic1()
    {
        return $this->belongsTo(Role::class, 'role_pic_1');
    }

    public function rolePic2()
    {
        return $this->belongsTo(Role::class, 'role_pic_2');
    }

    public function rolePic3()
    {
        return $this->belongsTo(Role::class, 'role_pic_3');
    }

    public function rolePic4()
    {
        return $this->belongsTo(Role::class, 'role_pic_4');
    }

    public function rolePic5()
    {
        return $this->belongsTo(Role::class, 'role_pic_5');
    }


    public function logs()
    {
        return $this->hasMany(Log::class, 'project_id', 'project_id');
    }
}
