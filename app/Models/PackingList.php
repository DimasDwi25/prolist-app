<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingList extends Model
{
    use HasFactory, ActivityLoggable;

    protected $primaryKey = 'pl_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pl_id',
        'pl_number',
        'pn_id',
        'destination',
        'expedition_name',
        'pl_date',
        'ship_date',
        'pl_type',
        'client_pic',
        'receive_date',
        'pl_return_date',
        'remark',
        'created_by',
        'int_pic'
    ];

    // Relasi
    public function project()
    {
        return $this->belongsTo(Project::class, 'pn_id', 'pn_number');
    }

    public function intPic()
    {
        return $this->belongsTo(User::class, 'client_pic');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
