<?php

namespace Database\Factories;

use App\Enums\MoodType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mood>
 */
class MoodFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'types' => fake()->randomElements(MoodType::cases(), fake()->numberBetween(1, 2)),
            'comment' => fake()->optional()->paragraph(),
            'user_id' => User::factory(),
        ];
    }
}
