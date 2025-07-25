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

    // Relasi ke Project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Relasi PIC - ubah nama method biar nggak bentrok dengan kolom
    public function pic1User()
    {
        return $this->belongsTo(User::class, 'pic1');
    }

    public function pic2User()
    {
        return $this->belongsTo(User::class, 'pic2');
    }

    public function pic3User()
    {
        return $this->belongsTo(User::class, 'pic3');
    }

    public function pic4User()
    {
        return $this->belongsTo(User::class, 'pic4');
    }

    public function pic5User()
    {
        return $this->belongsTo(User::class, 'pic5');
    }

    // Relasi Role PIC
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

    // Relasi Log (berdasarkan project_id)
    public function logs()
    {
        return $this->hasMany(Log::class, 'project_id', 'project_id');
    }
}
