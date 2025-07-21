<?php
namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ClientsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Client::select([
            'name',
            'address',
            'phone',
            'client_representative',
            'city',
            'province',
            'country',
            'zip_code',
            'web',
            'notes'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'name',
            'address',
            'phone',
            'client_representative',
            'city',
            'province',
            'country',
            'zip_code',
            'web',
            'notes'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
