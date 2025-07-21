<?php

namespace App\Imports;

use App\Models\Quotation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class QuotationsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Quotation([
            'no_quotation' => $row['no_quotation'],
            'quotation_date' => Carbon::parse($row['quotation_date']),
            'client_id' => $row['client_id'],
            'title_quotation' => $row['title_quotation'],
            'quotation_weeks' => $row['quotation_weeks'],
            'quotation_value' => $row['quotation_value'],
            'po_date' => Carbon::parse($row['po_date']),
            'sales_weeks' => $row['sales_weeks'],
            'po_number' => $row['po_number'],
            'po_value' => $row['po_value'],
            'revision_quotation_date' => Carbon::parse($row['revision_quotation_date']),
            'status' => $row['status'],
            'client_pic' => $row['client_pic'],
            'user_id' => $row['user_id'],
        ]);
    }
}
