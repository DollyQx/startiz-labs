<?php

namespace Database\Factories;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\Industry;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'client_id' => User::factory()->create(['role' => UserRole::CLIENT])->id,
            'service_id' => Service::factory(),
            'industry_id' => Industry::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => ProjectStatus::IN_PROGRESS,
            'priority' => ProjectPriority::MEDIUM,
            'start_date' => now()->subDays(5),
            'expected_completion_date' => now()->addDays(30),
            'estimated_value' => fake()->randomFloat(2, 50000, 500000),
        ];
    }
}
