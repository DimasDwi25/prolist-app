<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoldingTax extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'pph23_rate',
        'nilai_pph23',
        'pph42_rate',
        'nilai_pph42',
        'no_bukti_potong',
        'nilai_potongan',
        'tanggal_wht',
    ];

    protected $casts = [
        'pph23_rate' => 'decimal:4',
        'nilai_pph23' => 'decimal:2',
        'pph42_rate' => 'decimal:4',
        'nilai_pph42' => 'decimal:2',
        'nilai_potongan' => 'decimal:2',
        'tanggal_wht' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }
}
