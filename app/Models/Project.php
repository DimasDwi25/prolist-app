<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

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
            $project->project_number = self::generateProjectNumber();
        });
    }

    public static function generateProjectNumber(): string
    {
        $currentYear = now()->format('Y');
        $yearShort = now()->format('y');

        // Ambil project terakhir berdasarkan tahun
        $lastProject = self::whereYear('created_at', $currentYear)
            ->orderByDesc('created_at')
            ->first();

        $lastNumber = 0;

        if ($lastProject && preg_match('/PN-(\d{3})\/\d{2}/', $lastProject->project_number, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return "PN-{$newNumber}/{$yearShort}";
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




}
