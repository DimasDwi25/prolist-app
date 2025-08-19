<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\Project;
use App\Models\Quotation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ProjectsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Skip kalau kosong
            if (empty($row['project_number'])) {
                continue;
            }

            // Cek apakah quotations_id ada
            $quotationId = $row['quotations_id'] ?? null;

            if ($quotationId && !Quotation::where('quotation_number', $quotationId)->exists()) {
                Log::warning("Row {$index} dilewati karena quotations_id {$quotationId} tidak ditemukan.");
                continue; // skip row ini
            }


            Project::create([
                'pn_number'      => $row['pn_number'],   // langsung pakai dari Excel
    'project_number' => $row['project_number'],
                'project_name' => $row['project_name'],
                'categories_project_id' => !empty($row['categories_project_id']) ? (int) $row['categories_project_id'] : null,
                'quotations_id'    => $quotationId,
                'phc_dates'  => $this->normalizeDate($row['phc_dates']),
                'mandays_engineer' => !empty($row['mandays_engineer']) ? (int) $row['mandays_engineer'] : null,
                'mandays_technician' => !empty($row['mandays_technician']) ? (int) $row['mandays_technician'] : null,
                'target_dates' => $this->normalizeDate($row['target_dates']),
                'material_status' => $row['material_status'] ?? null,
                'dokumen_finish_date'    => $this->normalizeDate($row['dokumen_finish_date']),
                'engineering_finish_date'=> $this->normalizeDate($row['engineering_finish_date']),
                'jumlah_invoice' => !empty($row['jumlah_invoice']) ? (int) $row['jumlah_invoice'] : null,
                'status_project_id' => !empty($row['status_project_id']) ? (int) $row['status_project_id'] : 1, // default 1
                'project_progress' => !empty($row['project_progress']) ? (int) $row['project_progress'] : null,
                'po_date' => $this->normalizeDate($row['po_date']),
                'sales_weeks' => !empty($row['sales_weeks']) ? (int) $row['sales_weeks'] : null,
                'po_number' => $row['po_number'] ?? null,
                'po_value' => !empty($row['po_value']) ? (float) $row['po_value'] : null,
                'is_confirmation_order' => filter_var($row['is_confirmation_order'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'parent_pn_number' => !empty($row['parent_pn_number']) ? (int) $row['parent_pn_number'] : null,
                'client_id' => $this->resolveClient($row['client_id'] ?? null),
            ]);

        }
    }

    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Jika excel date number
        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($value));
        }

        // Mapping bulan Indonesia ke Inggris
        $bulan = [
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

        $value = strtr($value, $bulan);

        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null; // kalau tetap gagal, jangan bikin error
        }
    }

    private function resolveClient($value)
    {
        if (empty($value)) {
            return null; // atau bisa default ke 1 kalau wajib
        }

        // Kalau angka langsung dianggap ID
        if (is_numeric($value)) {
            return (int) $value;
        }

        // Kalau teks → coba cari di tabel clients berdasarkan nama
        $client = Client::where('name', $value)->first();

        return $client ? $client->id : null;
    }

    protected function parseIndonesianDate($value)
    {
        if (empty($value)) {
            return null;
        }

        $bulan = [
            'Januari'   => '01',
            'Februari'  => '02',
            'Maret'     => '03',
            'April'     => '04',
            'Mei'       => '05',
            'Juni'      => '06',
            'Juli'      => '07',
            'Agustus'   => '08',
            'September' => '09',
            'Oktober'   => '10',
            'November'  => '11',
            'Desember'  => '12',
        ];

        // Pisah "19 Agustus 2025" -> [19, Agustus, 2025]
        $parts = explode(' ', trim($value));
        if (count($parts) < 3) {
            return null; // kalau format tidak sesuai
        }

        $day   = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
        $month = $bulan[$parts[1]] ?? null;
        $year  = $parts[2];

        if (!$month) {
            return null; // bulan tidak valid
        }

        // Format ke Y-m-d (SQL Server friendly)
        return "{$year}-{$month}-{$day}";
    }

    function normalizeDate(?string $dateString): ?string
    {
        if (empty($dateString)) {
            return null; // biar bisa masuk ke kolom nullable
        }

        try {
            // Parsing semua format bahasa Indonesia / Inggris
            $carbon = Carbon::parse($dateString);

            // Format ke SQL Server-compatible
            return $carbon->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }


}
