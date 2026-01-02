<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'progress' => fake()->numberBetween(0, 100),
            'start_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'progress' => 100,
            'completed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function canceled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'canceled_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
