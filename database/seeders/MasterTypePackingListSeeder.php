<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterTypePackingList;

class MasterTypePackingListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Engineering', 'description' => 'Packing list for engineering department'],
            ['name' => 'Finance', 'description' => 'Packing list for finance department'],
            ['name' => 'Marketing', 'description' => 'Packing list for marketing department'],
            ['name' => 'SUC', 'description' => 'Packing list for SUC department'],
        ];

        foreach ($types as $type) {
            MasterTypePackingList::create($type);
        }
    }
}
