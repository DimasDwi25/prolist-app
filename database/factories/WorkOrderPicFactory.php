<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkOrderPic>
 */
class WorkOrderPicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // ambil role tertentu
        $allowedRoles = Role::whereIn('name', ['engineer', 'electrician', 'electrician_supervisor', 'drafter', 'engineer_supervisor'])->pluck('id');

        // ambil user yang memiliki role tersebut
        $allowedUsers = User::whereIn('role_id', $allowedRoles)->pluck('id');

        return [
            'work_order_id' => WorkOrder::inRandomOrder()->first()?->id ?? WorkOrder::factory(),
            'user_id' => $allowedUsers->isNotEmpty()
                ? $this->faker->randomElement($allowedUsers)
                : User::factory(),
            'role_id' => $this->faker->randomElement($allowedRoles->toArray()),
        ];
    }
}
