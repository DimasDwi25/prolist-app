<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'payment_number',
        'payment_date',
        'payment_amount',
        'currency',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($payment) {
            $payment->updateInvoicePaymentStatus();
        });

        static::deleted(function ($payment) {
            $payment->updateInvoicePaymentStatus();
        });
    }

    public function updateInvoicePaymentStatus()
    {
        $invoice = $this->invoice;
        $totalPaid = $invoice->payments()->sum('payment_amount');

        if ($totalPaid >= $invoice->invoice_value) {
            $invoice->update(['payment_status' => 'paid']);
        } elseif ($totalPaid > 0 && $totalPaid < $invoice->invoice_value) {
            $invoice->update(['payment_status' => 'partial']);
        } else {
            $invoice->update(['payment_status' => 'unpaid']);
        }
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }
}
