<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::with(['role', 'department'])
            ->get()
            ->map(function ($user) {
                return [
                    'ID' => $user->id,
                    'Name' => $user->name,
                    'Email' => $user->email,
                    'Role' => $user->role->name ?? '',
                    'Department' => $user->department->name ?? '',
                    'Created At' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '',
                ];
            });
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Role', 'Department', 'Created At'];
    }
}
