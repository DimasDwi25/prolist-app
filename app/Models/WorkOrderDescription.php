<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderDescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'description',
        'result',
    ];

    /**
     * Relasi ke Work Order
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
