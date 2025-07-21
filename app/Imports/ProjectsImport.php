<?php

namespace App\Imports;

use App\Models\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProjectsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Project([
            'project_name' => $row['project_name'],
            'categories_project_id' => $row['category_id'], // pastikan ID-nya valid
            'quotations_id' => $row['quotation_id'], // sesuaikan kolom
            'phc_dates' => $row['phc_dates'],
            'mandays_engineer' => $row['mandays_engineer'],
            'mandays_technician' => $row['mandays_technician'],
            'target_dates' => $row['target_dates'],
            'material_status' => $row['material_status'],
            'dokumen_finish_date' => $row['dokumen_finish_date'],
            'engineering_finish_date' => $row['engineering_finish_date'],
            'jumlah_invoice' => $row['jumlah_invoice'],
            'status_project_id' => $row['status_project_id'],
            'project_progress' => $row['project_progress'],
        ]);
    }
}

