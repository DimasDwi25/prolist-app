<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, ActivityLoggable;

    protected $primaryKey = 'invoice_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'invoice_id',
        'invoice_number_in_project',
        'project_id',
        'invoice_type_id',
        'no_faktur',
        'invoice_date',
        'invoice_description',
        'invoice_value',
        'invoice_due_date',
        'payment_status',
        'remarks',
        'currency',
        'ppn_rate',
        'pph23_rate',
        'pph42_rate',
        'rate_usd',
        'nilai_ppn',
        'nilai_pph23',
        'nilai_pph42',
        'total_invoice',
        'expected_payment',
        'payment_actual_date',
        'is_ppn',
        'is_pph23',
        'is_pph42',

    ];

    public function invoiceType()
    {
        return $this->belongsTo(InvoiceType::class, 'invoice_type_id', 'id');
    }

    public function retentions()
    {
        return $this->hasMany(Retention::class, 'invoice_id', 'invoice_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'pn_number');
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id', 'invoice_id');
    }
}
