<?php

namespace Database\Seeders;

use App\Models\StatusProject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        StatusProject::insert([
            ['name' => 'Draft'],
            ['name' => 'On Progress'],
            ['name' => 'Completed'],
            ['name' => 'Cancelled'],
            ['name' => 'Hold'],
            ['name' => 'Closed']
        ]);
    }
}
