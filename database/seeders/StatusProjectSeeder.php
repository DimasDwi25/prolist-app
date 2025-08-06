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
            ['name' => 'On Progress'],
            ['name' => 'Document Completed'],
            ['name' => 'Work Completed'],
            ['name' => 'Hold By Customer'],
            ['name' => 'Project Finished'],
        ]);
    }
}
