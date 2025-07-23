<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'inquiry_date',
        'client_id',
        'title_quotation',
        'quotation_date',
        'no_quotation',
        'quotation_weeks',
        'quotation_value',
        'po_date',
        'sales_weeks',
        'po_number',
        'po_value',
        'revision_quotation_date',
        'status',
        'client_pic',
        'user_id',
        'quotation_number',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($quotation) {
            $quotation->no_quotation = self::formatFullQuotationNo($quotation->no_quotation);
        });
    }

    public static function formatFullQuotationNo($number)
    {
        $romanMonth = self::getCurrentMonthRoman();
        $yearShort = now()->format('y');

        return 'Q-' . str_pad($number, 3, '0', STR_PAD_LEFT) . '/' . $romanMonth . '/' . $yearShort;
    }

    public static function getCurrentMonthRoman(): string
    {
        $roman = [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $roman[(int) now()->format('m')];
    }

    public static function getNextQuotationNumber(): int
    {
        $currentYear = now()->year;

        $last = self::whereYear('created_at', $currentYear)
            ->orderByDesc('created_at')
            ->first();

        if ($last && preg_match('/Q-(\d{3})/', $last->no_quotation, $matches)) {
            return (int) $matches[1] + 1;
        }

        return 1;
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function projects()
    {
        return $this->hasOne(Project::class, 'quotations_id');
    }

    protected $casts = [
        'inquiry_date' => 'datetime',
        'quotation_date' => 'datetime',
        'revision_quotation_date' => 'datetime',
        'po_date' => 'datetime',
    ];

}
