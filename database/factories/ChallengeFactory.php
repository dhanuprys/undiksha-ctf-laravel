<?php

namespace Database\Factories;

use App\Enums\ChallengeLevel;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'title' => 'Challenge '.Str::random(5),
            'description' => 'This is a dummy challenge description generated without faker. Find the flag.',
            'base_score' => mt_rand(1, 5) * 100,
            'difficulty' => [ChallengeLevel::Easy, ChallengeLevel::Medium, ChallengeLevel::Hard, ChallengeLevel::VeryHard][array_rand([ChallengeLevel::Easy, ChallengeLevel::Medium, ChallengeLevel::Hard, ChallengeLevel::VeryHard])],
            'flag' => 'CTF{'.Str::random(6).'_'.Str::random(6).'}',
            'is_active' => true,
        ];
    }
}
