<?php

namespace App\Imports;

use App\Models\Client;
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
        // Normalize keys to lowercase
        $row = array_change_key_case($row, CASE_LOWER);
        
        $noQuotation = isset($row['no_quotation']) ? trim($row['no_quotation']) : null;
        $quotationNumber = isset($row['quotation_number']) ? trim($row['quotation_number']) : null;
        $clientId = isset($row['client_id']) ? (int)$row['client_id'] : null;

        // Skip if no_quotation is empty
        if (empty($noQuotation)) {
            $this->failCount++;
            return null;
        }

        // Skip if already exists
        if (Quotation::where('no_quotation', $noQuotation)->exists()) {
            $this->failCount++;
            return null;
        }

        // Skip if client doesn't exist
        if (empty($clientId) || !Client::where('id', $clientId)->exists()) {
            $this->failCount++;
            return null;
        }

        $this->successCount++;

        Quotation::updateOrCreate(
            ['quotation_number' => $quotationNumber],
            [
                'no_quotation' => $noQuotation,
                'inquiry_date' => $this->parseDate($row['inquiry_date'] ?? null),
                'quotation_date' => $this->parseDate($row['quotation_date'] ?? null),
                'client_id' => $clientId,
                'title_quotation' => $row['title_quotation'] ?? null,
                'quotation_weeks' => $this->formatWeek($row['quotation_weeks'] ?? null),
                'quotation_value' => $this->formatNumber($row['quotation_value'] ?? 0),
                'revision_quotation_date' => $this->parseDate($row['revision_quotation_date'] ?? null),
                'status' => $row['status'] ?? null,
                'client_pic' => $row['client_pic'] ?? null,
                'revisi' => $row['revisi'],
                'user_id' => $row['user_id'] ?? null,
            ]
        );

        return null; // Karena data sudah diinsert/update, return null


    } catch (\Exception $e) {
        $this->failCount++;
        Log::error('Failed to import quotation', [
            'row_data' => $row,
            'error_message' => $e->getMessage(),
        ]);
        return null;
    }
}

private function formatNumber($value)
{
    if (empty($value) || $value == '-') {
        return 0;
    }
    
    // Remove commas and any non-numeric characters except decimal point
    $value = preg_replace('/[^0-9.]/', '', $value);
    
    return (float)$value;
}

    private function parseDate($value)
{
    if (empty($value) || $value == '30-Dec-99') {
        return null;
    }

    try {
        if (is_numeric($value)) {
            return Carbon::instance(
                \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
            );
        }
        
        // Handle "07-Mar-25" format
        if (preg_match('/\d{2}-\w{3}-\d{2}/', $value)) {
            return Carbon::createFromFormat('d-M-y', $value);
        }
        
        return Carbon::parse($value);
    } catch (\Exception $e) {
        $this->failCount++;
        Log::warning('Invalid date during import', [
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
