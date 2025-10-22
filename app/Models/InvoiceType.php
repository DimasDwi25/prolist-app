<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_type',
        'description',
    ];

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_invoice_type', 'invoice_type_id', 'invoice_id');
    }
}
