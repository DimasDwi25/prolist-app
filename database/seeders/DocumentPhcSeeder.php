<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentPhcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $documents = [
            'scope_of_work_approval',
            'organization_chart',
            'project_schedule',
            'component_list',
            'progress_claim_report',
            'component_approval_list',
            'design_approval_draw',
            'shop_draw',
            'fat_sat_forms',
            'daily_weekly_progress_report',
            'do_packing_list',
            'site_testing_commissioning_report',
            'as_build_draw',
            'manual_documentation',
            'accomplishment_report',
            'client_document_requirements',
            'job_safety_analysis',
            'risk_assessment',
            'tool_list',
        ];

        foreach ($documents as $doc) {
            DB::table('documents_phc')->insert([
                'name'       => $doc,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
