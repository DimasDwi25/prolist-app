<?php

namespace Database\Seeders;

use App\Models\PurposeWorkOrders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        PurposeWorkOrders::insert([
            ['name' => 'Meeting'],
            ['name' => 'Survey'],
            ['name' => 'Install'],
            ['name' => 'Test'],
        ]);
    }
}
