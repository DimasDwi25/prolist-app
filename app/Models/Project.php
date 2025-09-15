<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'pn_number';
    public $incrementing = false; // karena kita generate manual
    protected $keyType = 'int'; // karena pn_number integer

    protected $fillable = [
        'project_name',
        'project_number',
        'categories_project_id',
        'quotations_id',
        'phc_dates',
        'mandays_engineer',
        'mandays_technician',
        'target_dates',
        'material_status',
        'dokumen_finish_date',
        'engineering_finish_date',
        'jumlah_invoice',
        'status_project_id',
        'project_progress',
        'po_date',
        'sales_weeks',
        'po_number',
        'po_value',
        'is_confirmation_order',
        'parent_pn_number',
        'client_id',
        'pn_number',
    ];

    public function getRouteKeyName()
    {
        return 'pn_number';
    }

    protected static function booted(): void
    {
        static::creating(function ($project) {
            $isCO = filter_var($project->is_confirmation_order, FILTER_VALIDATE_BOOLEAN);
            $project->pn_number = self::generatePnNumber();
            $project->project_number = self::generateProjectNumber(
                $project->pn_number,
                $isCO
            );
        });


        static::updating(function ($project) {
            if (!empty($project->pn_number)) {
                $project->project_number = self::generateProjectNumber(
                    $project->pn_number,
                    (bool) $project->is_confirmation_order
                );
            }
        });
    }

    public static function generatePnNumber(): int
    {
        $yearShort = now()->format('y'); // '25'
        $start = (int) ($yearShort . '000');
        $end   = (int) ($yearShort . '999');

        $last = self::whereBetween('pn_number', [$start, $end])
            ->orderByDesc('pn_number')
            ->first();

        if ($last) {
            $lastNumber = (int) substr((string) $last->pn_number, 2);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return (int) ($yearShort . str_pad($newNumber, 3, '0', STR_PAD_LEFT));
    }


    public static function generateProjectNumber(int $pnNumber, bool $isCO = false): string
    {
        $yearShort = substr((string) $pnNumber, 0, 2);
        $number = substr((string) $pnNumber, 2);

        $prefix = $isCO ? 'CO-PN' : 'PN';
        return "{$prefix}-{$yearShort}/" . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field === 'project_number') {
            return $this->where('project_number', $value)->firstOrFail();
        }

        return $this->where($field ?? $this->getRouteKeyName(), (int) $value)->firstOrFail();
    }


    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotations_id', 'quotation_number');
    }

    public function category()
    {
        return $this->belongsTo(CategorieProject::class, 'categories_project_id');
    }

    public function phc()
    {
        return $this->hasOne(PHC::class, 'project_id', 'pn_number');
    }

    public function statusProject()
    {
        return $this->belongsTo(StatusProject::class, 'status_project_id');
    }

    public function logs()
    {
        return $this->hasMany(\App\Models\Log::class);
    }

    public function schedules()
    {
        return $this->hasMany(ProjectSchedule::class, 'project_id', 'pn_number');
    }


    // Parent project
    public function parent()
    {
        return $this->belongsTo(Project::class, 'parent_pn_number', 'pn_number');
    }

    // Child variations
    public function variants()
    {
        return $this->hasMany(Project::class, 'parent_pn_number', 'pn_number');
    }

    public function manPowerAllocations()
    {
        return $this->hasMany(ManPowerAllocation::class, 'project_id', 'pn_number');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    protected $casts = [
        'target_dates' => \App\Casts\IndonesianDateCast::class,
        'po_date' => \App\Casts\IndonesianDateCast::class,
        'phc_dates' => \App\Casts\IndonesianDateCast::class,
        'dokumen_finish_date' => \App\Casts\IndonesianDateCast::class,
        'engineering_finish_date' => \App\Casts\IndonesianDateCast::class,
        'created_at' => \App\Casts\IndonesianDateCast::class,
        'updated_at' => \App\Casts\IndonesianDateCast::class,
    ];


    // Parsing otomatis dari inputan bulan Indonesia → Carbon
    public function setAttribute($key, $value)
    {
        if (in_array($key, array_keys($this->casts)) && !empty($value)) {
            $value = $this->parseIndonesianDate($value);
        }
        return parent::setAttribute($key, $value);
    }

    // Getter otomatis → format tanggal pakai bulan Indonesia
    protected function asDateTime($value)
    {
        return parent::asDateTime($value);
    }

    public function getTargetDatesIndAttribute()
    {
        return $this->target_dates ? $this->target_dates->translatedFormat('d F Y') : null;
    }

    public function getPoDateIndAttribute()
    {
        return $this->po_date ? $this->po_date->translatedFormat('d F Y') : null;
    }



    // Fungsi parser untuk input
    protected function parseIndonesianDate($value)
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        $months = [
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

        $value = str_ireplace(array_keys($months), array_values($months), $value);

        return Carbon::parse($value);
    }

}
