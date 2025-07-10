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
            ['name' => 'Kick of Meeting'],
            ['name' => 'Komplain'], 
            ['name' => 'Additional Work'],
            ['name' => 'Plan Changing'], 
            ['name' => 'Status Material'], 
            ['name' => 'Status Shipment'],
            ['name' => 'Progress Project'],
            ['name' => 'Design/Drawing'],
            ['name' => 'Programming'],
            ['name' => 'FAT, SAT, Test.Comm'],
            ['name' => 'Additional Work'],
            ['name' => 'Site Installation'],   
            ['name' => 'Doc Status'],          
        ]);
    }
}
