<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRemark extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'remark',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'pn_number');
    }
}
