<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceType extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable = [
        'code_type',
        'description',
    ];

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_invoice_type', 'invoice_type_id', 'invoice_id');
    }
}
