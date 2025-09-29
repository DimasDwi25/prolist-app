<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurposeWorkOrders extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Relasi ke Work Orders
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'purpose_id');
    }
}
