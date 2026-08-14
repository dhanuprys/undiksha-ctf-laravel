<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Bulfest CTF '.date('Y'),
            'year' => date('Y'),
            'is_active' => (bool) mt_rand(0, 1),
            'start_time' => now()->subDays(mt_rand(1, 30)),
            'end_time' => now()->addDays(mt_rand(1, 30)),
        ];
    }
}
