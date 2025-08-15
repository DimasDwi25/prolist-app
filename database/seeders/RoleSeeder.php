<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Role::insert([
            ['name' => 'super_admin', 'type_role' => 1],
            ['name' => 'marketing_director', 'type_role' => 1],
            ['name' => 'manager_marketing', 'type_role' => 1],
            ['name' => 'sales_supervisor', 'type_role' => 1],
            ['name' => 'supervisor marketing', 'type_role' => 1],
            ['name' => 'marketing_estimator', 'type_role' => 1],
            ['name' => 'marketing_admin', 'type_role' => 1],
            ['name' => 'engineering_director', 'type_role' => 1],
            ['name' => 'project manager', 'type_role' => 1],
            ['name' => 'engineer_supervisor', 'type_role' => 1],
            ['name' => 'engineer', 'type_role' => 1],
            ['name' => 'drafter', 'type_role' => 1],
            ['name' => 'electrician_supervisor', 'type_role' => 1],
            ['name' => 'electrician', 'type_role' => 1],
            ['name' => 'site_engineer', 'type_role' => 1],
            ['name' => 'project controller', 'type_role' => 1],
            ['name' => 'engineering_admin', 'type_role' => 1],
            ['name' => 'suc_manager', 'type_role' => 1],
            ['name' => 'warehouse', 'type_role' => 1],
            ['name' => 'purchasing', 'type_role' => 1],
            ['name' => 'logistic', 'type_role' => 1],
            ['name' => 'hr_manager', 'type_role' => 1],
            ['name' => 'ga_supervisor', 'type_role' => 1],
            ['name' => 'hr_ga_admin', 'type_role' => 1],
            ['name' => 'ga_staff', 'type_role' => 1],
            ['name' => 'acc_fin_manager', 'type_role' => 1],
            ['name' => 'acc_fin_supervisor', 'type_role' => 1],
            ['name' => 'finance_administration', 'type_role' => 1],
            ['name' => 'project_manager', 'type_role' => 2],
            ['name' => 'site_manager', 'type_role' => 2],
            ['name' => 'site_supervisor', 'type_role' => 2],
            ['name' => 'site_admin', 'type_role' => 2],
            ['name' => 'foreman', 'type_role' => 2],
            ['name' => 'electrician', 'type_role' => 2],
            ['name' => 'project_controller', 'type_role' => 2],
            ['name' => 'document_controller', 'type_role' => 2],
            ['name' => 'hse', 'type_role' => 2],
            ['name' => 'quality_control', 'type_role' => 2],
            ['name' => 'site_warehouse', 'type_role' => 2],
        ]);

    }
}
