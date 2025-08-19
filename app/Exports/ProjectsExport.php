<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Project::with(['quotation', 'category', 'statusProject'])->get();
    }

    public function headings(): array
    {
        return [
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
            'client_id',
        ];
    }

    public function map($project): array
    {
        return [
            $project->project_name,
            $project->project_number,
            $project->categories_project_id, // foreign key, tampilkan name
            $project->quotations_id, // foreign key
            optional($project->phc_dates)->format('Y-m-d'),
            $project->mandays_engineer,
            $project->mandays_technician,
            optional($project->target_dates)->format('Y-m-d'),
            $project->material_status,
            optional($project->dokumen_finish_date)->format('Y-m-d'),
            optional($project->engineering_finish_date)->format('Y-m-d'),
            $project->jumlah_invoice,
            $project->status_project_id,
            $project->project_progress,
            optional($project->po_date)->format('Y-m-d'),
            $project->sales_weeks,
            $project->po_number,
            $project->po_value,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
