<?php

namespace App\Models;

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
    ];

    protected static function booted(): void
    {
        static::creating(function ($project) {
            $project->pn_number = self::generatePnNumber();
            $project->project_number = self::generateProjectNumber($project->pn_number);
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


    public static function generateProjectNumber(int $pnNumber): string
    {
        $yearShort = substr($pnNumber, 0, 2);
        $number = substr($pnNumber, 2);
        return "PN-{$yearShort}/" . str_pad($number, 3, '0', STR_PAD_LEFT);
    }



    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotations_id');
    }

    public function category()
    {
        return $this->belongsTo(CategorieProject::class, 'categories_project_id');
    }

    public function phc()
    {
        return $this->hasOne(PHC::class, 'project_id');
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
        return $this->hasMany(ProjectSchedule::class);
    }

    public function getRouteKeyName()
    {
        return 'pn_number';
    }


}
