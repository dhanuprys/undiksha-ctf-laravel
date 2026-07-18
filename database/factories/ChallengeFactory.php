<?php

namespace Database\Factories;

use App\Enums\ChallengeLevel;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Challenge>
 */
class ChallengeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'base_score' => fake()->numberBetween(1, 5) * 100,
            'difficulty' => fake()->randomElement([ChallengeLevel::Easy, ChallengeLevel::Medium, ChallengeLevel::Hard, ChallengeLevel::VeryHard]),
            'flag' => 'CTF{'.fake()->word().'_'.fake()->word().'}',
            'is_active' => true,
        ];
    }
}
