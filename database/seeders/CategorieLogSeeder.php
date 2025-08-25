<?php

namespace Database\Seeders;

use App\Models\CategorieLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorieLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        CategorieLog::insert([
            ['name' => 'Kick Off Meeting'],
            ['name' => 'Plan Changing'],
            ['name' => 'Material Status'],
            ['name' => 'Shipment/Delivery'],
            ['name' => 'Project Progress'],
            ['name' => 'Documentation'],
            ['name' => 'FAT'],
            ['name' => 'SAT'],
            ['name' => 'PLC Program'],
            ['name' => 'Application Programming'],
            ['name' => 'System Design'],
            ['name' => 'Drawing'],
            ['name' => 'Customer Complaint'],
            ['name' => 'Internal Complaint'],
            ['name' => 'Additional Work'],
            ['name' => 'Testing & Commisioning'],
            ['name' => 'Installation'],
            ['name' => 'Documents for Invoice'],
        ]);
    }
}
