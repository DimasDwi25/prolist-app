<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRemark extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable = [
        'project_id',
        'remark',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'pn_number');
    }
}
