<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Client([
            'name' => $row['name'] ?? null,
            'address' => $row['address'] ?? null,
            'phone' => $row['phone'] ?? null,
            'client_representative' => $row['client_representative'] ?? null,
            'city' => $row['city'] ?? null,
            'province' => $row['province'] ?? null,
            'country' => $row['country'] ?? null,
            'zip_code' => $row['zip_code'] ?? null,
            'web' => $row['web'] ?? null,
            'notes' => $row['notes'] ?? null,
        ]);
    }
}
