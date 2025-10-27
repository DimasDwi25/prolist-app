<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Tax::create([
            'name' => 'PPN',
            'rate' => 0.11,
        ]);

        \App\Models\Tax::create([
            'name' => 'PPh 23',
            'rate' => 0.0265,
        ]);

        \App\Models\Tax::create([
            'name' => 'PPh 4(2)',
            'rate' => 0.02,
        ]);
    }
}
