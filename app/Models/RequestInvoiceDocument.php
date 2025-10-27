<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestInvoiceDocument extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable = [
        'request_invoice_id',
        'document_preparation_id',
        'notes',
    ];

    protected $casts = [
    ];

    public function requestInvoice()
    {
        return $this->belongsTo(RequestInvoice::class, 'request_invoice_id');
    }

    public function documentPreparation()
    {
        return $this->belongsTo(DocumentPreparation::class, 'document_preparation_id');
    }
}
