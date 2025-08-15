<?php

namespace Database\Seeders;

use App\Models\CategorieProject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class projectCategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['name' => 'Material Supply', 'description' => 'testing'],
            ['name' => 'Software Development', 'description' => 'testing'],
            ['name' => 'Panel Manufacturing', 'description' => 'Testing'],
            ['name' => 'Service (Repair/Troubleshooting)', 'description' => 'Testing'],
            ['name' => 'Installation', 'description' => 'Testing'],
            ['name' => 'Turn Key Project', 'description' => 'Testing'],
            ['name' => 'Engineering Work', 'description' => 'testing'],
        ];

        foreach ($categories as $category) {
            CategorieProject::updateOrCreate(
                ['name' => $category['name']], // gunakan name sebagai key unik
                ['description' => $category['description']]
            );
        }
    }
}
