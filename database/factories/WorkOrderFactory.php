<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\PurposeWorkOrders;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = WorkOrder::class;

    public function definition(): array
    {
        $project = Project::inRandomOrder()->first() ?? Project::factory()->create();
        $purpose = PurposeWorkOrders::inRandomOrder()->first();

        // Ambil tahun dua digit
        $year = now()->format('y');
        $projectCode = str_pad(substr($project->pn_number ?? fake()->numberBetween(1, 999), -3), 3, '0', STR_PAD_LEFT);

        // Cari WO terakhir di project & tahun berjalan
        $lastWo = WorkOrder::where('project_id', $project->pn_number)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastWo ? (($lastWo->wo_number_in_project ?? 0) + 1) : 1;

        // Format WO code seperti di store
        $woKodeNo = sprintf("WO/%s/%s/%03d", $year, $projectCode, $nextNumber);

        // Ambil role tertentu
        $picRoles = Role::whereIn('name', [
            'engineer',
            'electrician',
            'electrician_supervisor',
            'drafter',
            'engineer_supervisor',
        ])->pluck('id');

        // Ambil user dari role tersebut
        $creatorUsers = User::whereIn('role_id', $picRoles)->pluck('id');

        // Ambil user project manager untuk accepted_by
        $projectManagerRole = Role::where('name', 'project manager')->value('id');
        $acceptedUsers = User::where('role_id', $projectManagerRole)->pluck('id');

        return [
            'project_id' => $project->pn_number,
            'purpose_id' => $purpose?->id,
            'wo_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'wo_number_in_project' => $nextNumber,
            'wo_kode_no' => $woKodeNo,

            'location' => fake()->city(),
            'vehicle_no' => fake()->optional()->bothify('B #### ??'),
            'driver' => fake()->optional()->name(),

            'total_mandays_eng' => fake()->numberBetween(1, 10),
            'total_mandays_elect' => fake()->numberBetween(0, 8),
            'add_work' => fake()->boolean(10),

            'approved_by' => User::inRandomOrder()->first()?->id,
            'status' => fake()->randomElement([
                WorkOrder::STATUS_WAITING_APPROVAL,
                WorkOrder::STATUS_APPROVED,
                WorkOrder::STATUS_WAITING_CLIENT,
                WorkOrder::STATUS_FINISHED,
            ]),

            'start_work_time' => fake()->optional(0.7)->dateTimeBetween('-2 months', 'now'),
            'stop_work_time' => fake()->optional(0.5)->dateTimeBetween('-1 months', 'now'),

            'continue_date' => fake()->optional(0.2)->date(),
            'continue_time' => fake()->optional(0.2)->time(),

            'client_note' => fake()->optional(0.3)->sentence(),
            'scheduled_start_working_date' => ($d1 = fake()->optional()->dateTimeBetween('-1 month', 'now')) ? $d1->format('Y-m-d') : null,
            'scheduled_end_working_date' => ($d2 = fake()->optional()->dateTimeBetween('now', '+1 month')) ? $d2->format('Y-m-d') : null,

            'actual_start_working_date' => ($d3 = fake()->optional()->dateTimeBetween('-1 month', 'now')) ? $d3->format('Y-m-d') : null,
            'actual_end_working_date' => ($d4 = fake()->optional()->dateTimeBetween('now', '+2 weeks')) ? $d4->format('Y-m-d') : null,


            'accomodation' => fake()->optional(0.3)->sentence(5),
            'material_required' => fake()->optional(0.5)->sentence(6),

            'wo_count' => fake()->numberBetween(1, 3),
            'client_approved' => fake()->boolean(60),

            'created_by' => $creatorUsers->isNotEmpty()
                ? fake()->randomElement($creatorUsers)
                : User::factory(),

            'accepted_by' => $acceptedUsers->isNotEmpty()
                ? fake()->randomElement($acceptedUsers)
                : User::factory(),
        ];
    }
}
