<?php

namespace Database\Factories;

use App\Models\Challenge;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'team_id' => Team::factory(),
            'challenge_id' => Challenge::factory(),
            'submitted_flag' => fake()->word(),
            'is_correct' => false,
            'points_awarded' => 0,
        ];
    }

    public function correct(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => true,
            'points_awarded' => fake()->numberBetween(50, 500),
        ]);
    }

    public function incorrect(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => false,
            'points_awarded' => fake()->numberBetween(-50, -10),
        ]);
    }
}
