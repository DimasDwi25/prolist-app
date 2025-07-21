<?php

namespace App\Exports;

use App\Models\Quotation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;


class QuotationsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithMapping
{
    public function collection()
    {
        return Quotation::select([
            'no_quotation',
            'quotation_date',
            'client_id',
            'title_quotation',
            'quotation_weeks',
            'quotation_value',
            'po_date',
            'sales_weeks',
            'po_number',
            'po_value',
            'revision_quotation_date',
            'status',
            'client_pic',
            'user_id',
        ])->get();
    }

    public function headings(): array
    {
        return [
            'no_quotation',
            'quotation_date',
            'client_id',
            'title_quotation',
            'quotation_weeks',
            'quotation_value',
            'po_date',
            'sales_weeks',
            'po_number',
            'po_value',
            'revision_quotation_date',
            'status',
            'client_pic',
            'user_id',
        ];
    }

    public function map($quotation): array
    {
        return [
            $quotation->no_quotation,
            optional($quotation->quotation_date)->format('Y-m-d'),
            $quotation->client_id,
            $quotation->title_quotation,
            $quotation->quotation_weeks,
            $quotation->quotation_value,
            optional($quotation->po_date)->format('Y-m-d'),
            $quotation->sales_weeks,
            $quotation->po_number,
            $quotation->po_value,
            optional($quotation->revision_quotation_date)->format('Y-m-d'),
            $quotation->status,
            $quotation->client_pic,
            $quotation->user_id,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
