<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Department;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::insert([
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role_id' => 1,
                'department_id' => 1,
            ],
            [
                'name' => 'Marketing',
                'email' => 'marketing@example.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
                'department_id' => 2,
            ]
        ]);
    }
}
