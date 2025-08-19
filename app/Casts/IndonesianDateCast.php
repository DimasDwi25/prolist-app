<?php

namespace App\Casts;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class IndonesianDateCast implements CastsAttributes
{
    protected $formats = [
        'd-m-Y',
        'd/m/Y',
        'd-m-Y H:i',
        'd/m/Y H:i',
        'd-m-Y H:i:s',
        'd/m/Y H:i:s',
    ];

    protected $bulan = [
        'Januari' => 'January',
        'Februari' => 'February',
        'Maret' => 'March',
        'April' => 'April',
        'Mei' => 'May',
        'Juni' => 'June',
        'Juli' => 'July',
        'Agustus' => 'August',
        'September' => 'September',
        'Oktober' => 'October',
        'November' => 'November',
        'Desember' => 'December',
    ];

    public function get($model, string $key, $value, array $attributes)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (!$value) {
            return null;
        }

        // ubah nama bulan Indonesia ke Inggris biar Carbon bisa baca
        $value = str_ireplace(array_keys($this->bulan), array_values($this->bulan), $value);

        // coba parse dengan beberapa format
        foreach ($this->formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                continue;
            }
        }

        // fallback parse bebas
        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
