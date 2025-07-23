<?php

namespace App\Imports;

use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class QuotationsImport implements ToModel, WithHeadingRow
{
    use Importable;

    public $successCount = 0;
    public $failCount = 0;

    public function model(array $row)
    {
        try {
            // Normalisasi key & value
            $row = array_change_key_case($row, CASE_LOWER);
            $noQuotation = isset($row['no_quotation']) ? trim($row['no_quotation']) : null;

            // Skip jika no_quotation kosong
            if (empty($noQuotation)) {
                $this->failCount++;
                return null;
            }

            // Skip jika sudah ada (hindari duplikat)
            if (Quotation::where('no_quotation', $noQuotation)->exists()) {
                $this->failCount++;
                return null;
            }

            $this->successCount++;

            return new Quotation([
                'no_quotation' => $noQuotation,
                'quotation_date' => $this->parseDate($row['quotation_date'] ?? null),
                'client_id' => $row['client_id'] ?? null,
                'title_quotation' => $row['title_quotation'] ?? null,
                'quotation_weeks' => $this->formatWeek($row['quotation_weeks'] ?? null),
                'quotation_value' => $row['quotation_value'] ?? 0,
                'po_date' => $this->parseDate($row['po_date'] ?? null),
                'sales_weeks' => $row['sales_weeks'] ?? null,
                'po_number' => $row['po_number'] ?? null,
                'po_value' => $row['po_value'] ?? 0,
                'revision_quotation_date' => $this->parseDate($row['revision_quotation_date'] ?? null),
                'status' => $row['status'] ?? null,
                'client_pic' => $row['client_pic'] ?? null,
                'user_id' => $row['user_id'] ?? null,
            ]);

        } catch (\Exception $e) {
            $this->failCount++;

            Log::error('Gagal import quotation', [
                'row_data' => $row,
                'error_message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                );
            }
            return Carbon::parse($value);
        } catch (\Exception $e) {
            $this->failCount++;
            Log::warning('Tanggal tidak valid saat import', [
                'value' => $value,
                'error_message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function formatWeek($week)
    {
        if (empty($week) || $week == 0) {
            return null;
        }
        return 'W-' . $week;
    }
}
