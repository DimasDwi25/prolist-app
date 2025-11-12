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
        'destination_id',
        'expedition_id',
        'pl_date',
        'ship_date',
        'pl_type_id',
        'int_pic',
        'client_pic',
        'receive_date',
        'pl_return_date',
        'remark',
        'created_by',
    ];

    // Relasi
    public function project()
    {
        return $this->belongsTo(Project::class, 'pn_id', 'pn_number');
    }

    public function expedition()
    {
        return $this->belongsTo(MasterExpedition::class, 'expedition_id');
    }

    public function plType()
    {
        return $this->belongsTo(MasterTypePackingList::class, 'pl_type_id');
    }

    public function intPic()
    {
        return $this->belongsTo(User::class, 'int_pic');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }
}
