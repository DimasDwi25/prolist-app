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
                'name' => 'Andry',
                'email' => 'andry@citasys.com',
                'password' => bcrypt('AndTM#1899'),
                'role_id' => 1,
                'department_id' => 1,
                'pin' => 727813
            ],
        ]);
    }
}
