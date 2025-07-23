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
            'name' => $row['name'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'client_representative' => $row['client_representative'],
            'city' => $row['city'],
            'province' => $row['province'],
            'country' => $row['country'],
            'zip_code' => $row['zip_code'],
            'web' => $row['web'],
            'notes' => $row['notes'],
        ]);
    }
}
