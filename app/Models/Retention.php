<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retention extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable = [
        'invoice_id',
        'retention_percentage',
        'retention_amount',
        'retention_due_date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }
}
