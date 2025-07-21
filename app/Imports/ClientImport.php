<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;

class ClientImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Client([
            'name' => $row[0],
            'address' => $row[1],
            'phone' => $row[2],
            'client_representative' => $row[3],
            'city' => $row[4],
            'province' => $row[5],
            'country' => $row[6],
            'postal_code' => $row[7],
            'website' => $row[8],
            'notes' => $row[9],
        ]);
    }
}
