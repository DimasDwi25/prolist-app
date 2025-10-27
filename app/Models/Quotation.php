<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory, ActivityLoggable;
    protected $primaryKey = 'quotation_number';
    public $incrementing = false; // karena kita generate manual
    protected $keyType = 'string'; 
    protected $fillable = [
        'inquiry_date',
        'client_id',
        'title_quotation',
        'quotation_date',
        'no_quotation',
        'quotation_weeks',
        'quotation_value',
        'revision_quotation_date',
        'status',
        'client_pic',
        'user_id',
        'revisi'
        
    ];
    protected $attributes = [
        'status' => 'O',
    ];
    public function getRouteKeyName(): string
    {
        return 'quotation_number';
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($quotation) {
            $date = $quotation->quotation_date ?? now();
            $year = $date->format('Y');

            if (empty($quotation->quotation_number)) {
                // Nomor belum diisi, auto-generate
                $number = self::getNextQuotationNumberForYear($year);
                $quotation->no_quotation = self::formatFullQuotationNo($number, $date);
                $quotation->quotation_number = $year . str_pad($number, 3, '0', STR_PAD_LEFT);
            } else {
                // Nomor manual diisi, pastikan format no_quotation sesuai
                $manualNumber = substr($quotation->quotation_number, 4); // misal: 203 => ambil 203
                $quotation->no_quotation = self::formatFullQuotationNo((int)$manualNumber, $date);
            }
        });
    }

    public static function getNextQuotationNumberForYear($year = null): int
    {
        $year = $year ?? now()->year;

        $lastQuotation = self::where('quotation_number', 'like', "$year%")
            ->orderByDesc('quotation_number')
            ->first();

        if ($lastQuotation) {
            // Ambil 3 digit terakhir dari quotation_number
            $lastNumber = (int)substr($lastQuotation->quotation_number, -3);
            return $lastNumber + 1;
        }

        return 1; // jika belum ada
    }

    public static function convertMonthToRoman($month): string
    {
        $roman = [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $roman[(int) $month];
    }
    
    public static function formatFullQuotationNo($number, $date = null)
    {
        // Jika date sudah merupakan Carbon instance, gunakan langsung
        // Jika string, parse ke Carbon
        if (!$date instanceof Carbon) {
            try {
                $date = Carbon::parse($date);
            } catch (\Exception $e) {
                // Jika gagal parse (misal: "VII"), fallback ke now()
                $date = now();
            }
        }

        $romanMonth = self::convertMonthToRoman($date->format('m'));
        $yearShort = $date->format('y');

        return 'Q-' . str_pad($number, 3, '0', STR_PAD_LEFT) . '/' . $romanMonth . '/' . $yearShort;
    }

    public static function getNextQuotationNumber()
    {
        // Ambil quotation_number terbesar
        $lastQuotation = self::orderBy('quotation_number', 'desc')->first();

        if ($lastQuotation) {
            // Ambil 3 digit terakhir
            $lastNumber = (int)substr($lastQuotation->quotation_number, -3);
            return $lastNumber + 1;
        }

        // Default kalau belum ada data
        return 1;
    }
    function romanToMonthNumber($roman)
    {
        $map = [
            'I' => 1,
            'II' => 2,
            'III' => 3,
            'IV' => 4,
            'V' => 5,
            'VI' => 6,
            'VII' => 7,
            'VIII' => 8,
            'IX' => 9,
            'X' => 10,
            'XI' => 11,
            'XII' => 12,
        ];

        return $map[$roman] ?? null;
    }
    public static function generateQuotationNumber(): string
    {
        $year = now()->format('Y');
        $prefix = $year;

        $lastQuotation = self::whereYear('created_at', $year)
            ->orderByDesc('created_at')
            ->first();

        $nextNumber = 1;

        if ($lastQuotation && preg_match('/^' . $year . '(\d{3})$/', $lastQuotation->quotation_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function projects()
    {
        return $this->hasOne(Project::class, 'quotations_id');
    }

    public function scopeWhereYearFromQuotation($query, $year)
    {
        // quotation_number = 4 digit tahun + 3 digit urut
        return $query->whereRaw("LEFT(CONVERT(VARCHAR(20), quotation_number), 4) = ?", [$year]);
    }


    protected $casts = [
        'inquiry_date' => 'date',
        'quotation_date' => 'date',
        'revision_quotation_date' => 'date',
    ];

}
