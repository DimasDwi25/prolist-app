<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $primaryKey = 'quotation_number';
    public $incrementing = false; // karena kita generate manual
    protected $keyType = 'int'; // karena quotation_number integer

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

            if (!empty($quotation->no_quotation)) {
                // Kalau user isi manual, sinkronkan ke quotation_number
                if (preg_match('/Q-(\d{3})/', $quotation->no_quotation, $matches)) {
                    $number = (int) $matches[1];
                } else {
                    // Kalau format salah, fallback ke generator
                    $number = self::getNextQuotationNumberForYear($date->format('Y'));
                    $quotation->no_quotation = self::formatFullQuotationNo($number, $date);
                }
            } else {
                // Kalau kosong, generate otomatis
                $number = self::getNextQuotationNumberForYear($date->format('Y'));
                $quotation->no_quotation = self::formatFullQuotationNo($number, $date);
            }

            // Sinkron quotation_number (YYYY + 3 digit)
            $quotation->quotation_number = $date->format('Y') . str_pad($number, 3, '0', STR_PAD_LEFT);
        });
    }



    public static function getNextQuotationNumberForYear($year): int
    {
        $lastQuotation = self::whereYear('quotation_date', $year)
            ->orderByRaw('CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(no_quotation, "/", 1), "-", -1) AS UNSIGNED) DESC')
            ->first();

        if ($lastQuotation && preg_match('/Q-(\d{3})/', $lastQuotation->no_quotation, $matches)) {
            return (int) $matches[1] + 1;
        }

        return 1;
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

    protected $casts = [
        'inquiry_date' => 'date',
        'quotation_date' => 'date',
        'revision_quotation_date' => 'date',
    ];

}
