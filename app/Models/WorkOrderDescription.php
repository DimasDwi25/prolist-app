<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderDescription extends Model
{
    use HasFactory, ActivityLoggable;

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
