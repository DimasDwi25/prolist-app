<?php

namespace App\Imports;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProjectsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip rows with empty project number
            if (empty($row['project_number'])) {
                continue;
            }

            Project::updateOrCreate(
                ['project_number' => $row['project_number']],
                [
                    'project_name' => $row['project_name'],
                    'categories_project_id' => $row['categories_project_id'],
                    'quotations_id' => $row['quotations_id'],
                    'mandays_engineer' => $row['mandays_engineer'],
                    'mandays_technician' => $row['mandays_technician'],
                    'target_dates' => $this->parseDate($row['target_dates']),
                    'material_status' => $row['material_status'],
                    'dokumen_finish_date' => $this->parseDate($row['dokumen_finish_date']),
                    'engineering_finish_date' => $this->parseDate($row['engineering_finish_date']),
                    'jumlah_invoice' => $row['jumlah_invoice'],
                    'status_project_id' => $row['status_project_id'],
                    'project_progress' => $row['project_progress'],
                ]
            );
        }
    }

    private function parseDate($date)
    {
        if (empty($date) || str_contains($date, '=VLOOKUP')) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}