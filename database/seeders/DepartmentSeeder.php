<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Department::insert([
            ['name' => 'MARKETING'
        ],
        [
            'name' => 'ENGINEERING'
        ],
        [
            'name' => 'SUPPLY CHAIN'
        ],
        [
            'name' => 'ACC FIN'
        ],
        [
            'name' => 'HRGA'
        ],
        [
            'name' => 'TOP MANAGEMENT'
        ],
        [
            'name' => 'CMP'
        ],

        [
            'name' => 'SUC-HR'
        ],

        ]);
    }
}
