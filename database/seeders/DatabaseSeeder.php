<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\Event;
use App\Models\Setting;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin
        User::factory()->admin()->create([
            'name' => 'Admin CTF',
            'email' => 'admin@undiksha.ac.id',
            'password' => bcrypt('password'),
        ]);

        // 2. Create Active Event
        $event = Event::factory()->create([
            'name' => 'Bulfest CTF 2026',
            'year' => 2026,
            'is_active' => true,
        ]);

        // 3. Create Event Settings
        Setting::factory()->create(['event_id' => $event->id, 'key' => 'degradation_rate', 'value' => '0.10']);
        Setting::factory()->create(['event_id' => $event->id, 'key' => 'penalty_deduction', 'value' => '5']);

        // 4. Create Categories
        $categories = Category::factory(5)->create();

        // 5. Create Challenges
        $challenges = collect();
        foreach ($categories as $category) {
            $challenges = $challenges->merge(
                Challenge::factory(3)->create([
                    'event_id' => $event->id,
                    'category_id' => $category->id,
                ])
            );
        }

        // 6. Create Teams & Participants
        $teams = Team::factory(8)->create([
            'event_id' => $event->id,
        ]);

        foreach ($teams as $team) {
            $users = User::factory(3)->participant()->create();
            $team->users()->attach($users);
        }

        // 7. Create Sample Submissions (mix of correct and incorrect)
        foreach ($teams as $team) {
            // Each team tries some challenges
            $attemptedChallenges = $challenges->random(5);

            foreach ($attemptedChallenges as $challenge) {
                $isCorrect = fake()->boolean(70); // 70% chance to be correct

                Submission::factory()
                    ->state(['is_correct' => $isCorrect])
                    ->create([
                        'user_id' => $team->users->first()->id,
                        'team_id' => $team->id,
                        'challenge_id' => $challenge->id,
                        'submitted_flag' => $isCorrect ? $challenge->flag : 'CTF{wrong_flag}',
                        'points_awarded' => $isCorrect ? $challenge->base_score : -5,
                    ]);
            }
        }
    }
}
