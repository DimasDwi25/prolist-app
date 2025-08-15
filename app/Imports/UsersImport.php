<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Cek kalau ada field yang null atau kosong → skip
        if (
            empty($row['name']) ||
            empty($row['email']) ||
            empty($row['password']) ||
            empty($row['role_id']) ||
            empty($row['department_id']) ||
            empty($row['pin'])
        ) {
            return null; // Laravel Excel akan skip row ini
        }

        return new User([
            'name'         => $row['name'],
            'email'        => $row['email'],
            'password'     => Hash::make($row['password']),
            'role_id'      => $row['role_id'],
            'department_id'=> $row['department_id'],
            'pin'          => $row['pin'],
        ]);
    }
}
