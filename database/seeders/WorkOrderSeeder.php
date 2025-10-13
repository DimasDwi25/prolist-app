<?php

namespace Database\Seeders;

use App\Models\WorkOrder;
use App\Models\WorkOrderDescription;
use App\Models\WorkOrderPic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
         // buat 100 Work Orders
        WorkOrder::factory(100)->create()->each(function ($wo) {
            // tiap WO punya 2–5 PIC
            WorkOrderPic::factory(fake()->numberBetween(2, 5))->create([
                'work_order_id' => $wo->id,
            ]);

            // tiap WO punya 1–3 deskripsi
            WorkOrderDescription::factory(fake()->numberBetween(1, 3))->create([
                'work_order_id' => $wo->id,
            ]);
        });
    }
}
