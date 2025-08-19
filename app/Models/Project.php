<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'pn_number';
    public $incrementing = false; // karena kita generate manual
    protected $keyType = 'string'; // karena pn_number integer

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
    ];

    public function getRouteKeyName()
    {
        return 'pn_number';
    }

    protected static function booted(): void
    {
        static::creating(function ($project) {
            $project->pn_number = self::generatePnNumber();
            $project->project_number = self::generateProjectNumber(
                $project->pn_number,
                (bool) $project->is_confirmation_order // pastikan boolean
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
        $yearShort = now()->format('y'); // contoh: '25' untuk 2025

        // Ambil project terakhir untuk tahun ini
        $last = self::whereRaw("LEFT(pn_number, 2) = ?", [$yearShort])
            ->orderByDesc('pn_number')
            ->first();

        if ($last) {
            // Ambil nomor urut setelah 2 digit tahun
            $lastNumber = (int) substr($last->pn_number, 2);
            $newNumber = $lastNumber + 1;
        } else {
            // Kalau belum ada data tahun ini, mulai dari 1
            $newNumber = 1;
        }

        // Gabungkan: tahun singkat + nomor urut 3 digit
        return (int) ($yearShort . str_pad($newNumber, 3, '0', STR_PAD_LEFT));
    }

    public static function generateProjectNumber(int $pnNumber, bool $isCO = false): string
    {
        $yearShort = substr($pnNumber, 0, 2);
        $number = substr($pnNumber, 2);

        $prefix = $isCO ? 'CO-PN' : 'PN';
        return "{$prefix}-{$yearShort}/" . str_pad($number, 3, '0', STR_PAD_LEFT);
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
        'target_dates' => 'datetime',
        'po_date' => 'datetime',
        'phc_dates' => 'datetime',
        'dokumen_finish_date' => 'datetime',
        'engineering_finish_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
