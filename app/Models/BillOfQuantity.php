<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillOfQuantity extends Model
{
    use HasFactory, ActivityLoggable;

    protected $table = 'bill_of_quantitys';

    protected $fillable = [
        'project_id',
        'item_number',
        'description',
        'material_value',
        'engineer_value',
        'material_portion',
        'engineer_portion',
        'progress_material',
        'progress_engineer',
        'total_progress',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'pn_number');
    }
}
