<?php

namespace Database\Seeders;

use App\Enums\ChallengeLevel;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\ChallengeAttachment;
use App\Models\Event;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Create an Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );

        // Common Testers for blackbox testing
        $tester1 = User::firstOrCreate(
            ['email' => 'tester1@example.com'],
            ['name' => 'Tester One', 'password' => Hash::make('password'), 'email_verified_at' => now(), 'is_admin' => false]
        );

        $tester2 = User::firstOrCreate(
            ['email' => 'tester2@example.com'],
            ['name' => 'Tester Two', 'password' => Hash::make('password'), 'email_verified_at' => now(), 'is_admin' => false]
        );

        $tester3 = User::firstOrCreate(
            ['email' => 'tester3@example.com'],
            ['name' => 'Tester Three', 'password' => Hash::make('password'), 'email_verified_at' => now(), 'is_admin' => false]
        );

        // Situation: Orphan User (Not part of any team)
        $orphanUser = User::firstOrCreate(
            ['email' => 'orphan@example.com'],
            ['name' => 'Orphan User', 'password' => Hash::make('password'), 'email_verified_at' => now(), 'is_admin' => false]
        );

        // 1. Create Categories
        $categories = collect(['Web Exploitation', 'Reverse Engineering', 'Cryptography', 'Forensics', 'Miscellaneous'])->map(function ($name) {
            return Category::firstOrCreate(
                ['name' => $name],
                ['description' => "Challenges related to $name."]
            );
        });

        // 2. Events (Active, Past, Upcoming)
        $activeEvent = Event::factory()->create([
            'name' => 'Dummy Active CTF '.date('Y').' - '.Str::random(4), // Ensure unique names
            'year' => date('Y'),
            'is_active' => true,
            'start_time' => now()->subDays(2),
            'end_time' => now()->addWeek(), // active until exactly one more week from today
        ]);

        $pastEvent = Event::factory()->create([
            'name' => 'Dummy Past CTF '.(date('Y') - 1).' - '.Str::random(4),
            'year' => date('Y') - 1,
            'is_active' => false,
            'start_time' => now()->subMonths(3)->subDays(2),
            'end_time' => now()->subMonths(3)->addDays(5),
        ]);

        $upcomingEvent = Event::factory()->create([
            'name' => 'Dummy Upcoming CTF '.(date('Y') + 1).' - '.Str::random(4),
            'year' => date('Y') + 1,
            'is_active' => false,
            'start_time' => now()->addMonths(2)->subDays(2),
            'end_time' => now()->addMonths(2)->addDays(5),
        ]);

        $events = collect([$activeEvent, $pastEvent]);

        // Seed basic settings for each event
        foreach ($events->push($upcomingEvent) as $evt) {
            \App\Models\Setting::firstOrCreate(['event_id' => $evt->id, 'key' => 'degradation_rate'], ['value' => '0.10']);
            \App\Models\Setting::firstOrCreate(['event_id' => $evt->id, 'key' => 'penalty_deduction'], ['value' => '0.05']);
            \App\Models\Setting::firstOrCreate(['event_id' => $evt->id, 'key' => 'max_team_size'], ['value' => '3']);
            \App\Models\Setting::firstOrCreate(['event_id' => $evt->id, 'key' => 'show_solver_count'], ['value' => '1']);
        }
        
        // Remove upcomingEvent from main processing loop array
        $events->pop();

        $realChallenges = [
            'Web Exploitation' => [
                ['title' => 'SQLi Login', 'desc' => 'Can you bypass the login screen? <br/> `SELECT * FROM users WHERE username = \'$user\' AND password = \'$pass\'`', 'difficulty' => ChallengeLevel::Easy, 'score' => 100, 'flag' => 'CTF{sqli_is_easy_123}'],
                ['title' => 'Directory Traversal', 'desc' => 'Find the secret file hidden in the server directory structure. The server uses `include($_GET["page"])`.', 'difficulty' => ChallengeLevel::Medium, 'score' => 250, 'flag' => 'CTF{lfi_to_rce_magic}'],
                ['title' => 'SSTI', 'desc' => 'The server reflects your input using Jinja2 templates. Can you read the flag file?', 'difficulty' => ChallengeLevel::Hard, 'score' => 500, 'flag' => 'CTF{template_injection_master}'],
            ],
            'Reverse Engineering' => [
                ['title' => 'Simple XOR', 'desc' => 'The binary XORs the flag with a single byte key. Find the key and decode it.', 'difficulty' => ChallengeLevel::Easy, 'score' => 100, 'flag' => 'CTF{xor_the_world}'],
                ['title' => 'ELF Analysis', 'desc' => 'We found this suspicious Linux binary. Can you figure out what password it wants?', 'difficulty' => ChallengeLevel::Medium, 'score' => 250, 'flag' => 'CTF{ghidra_is_your_friend}'],
                ['title' => 'Anti-Debugging', 'desc' => 'This binary refuses to run under `gdb`. Can you bypass the checks?', 'difficulty' => ChallengeLevel::Hard, 'score' => 500, 'flag' => 'CTF{ptrace_me_if_you_can}'],
            ],
            'Cryptography' => [
                ['title' => 'Caesar Cipher', 'desc' => 'Decrypt this message: `PGS{pnrfne_pvcure_vf_byq}`', 'difficulty' => ChallengeLevel::Easy, 'score' => 100, 'flag' => 'CTF{caesar_cipher_is_old}'],
                ['title' => 'RSA Factorization', 'desc' => 'We intercepted an RSA public key with a very small modulus `N = 3233`, `e = 17`. The ciphertext is `2790`. Decrypt it.', 'difficulty' => ChallengeLevel::Medium, 'score' => 250, 'flag' => 'CTF{rsa_small_n_65}'],
                ['title' => 'AES ECB Penguin', 'desc' => 'We encrypted a bitmap image with AES ECB mode. What can you see?', 'difficulty' => ChallengeLevel::Hard, 'score' => 500, 'flag' => 'CTF{never_use_ecb_mode}'],
            ],
            'Forensics' => [
                ['title' => 'Magic Bytes', 'desc' => 'This file has no extension and won\'t open. Fix its header to reveal the image.', 'difficulty' => ChallengeLevel::Easy, 'score' => 100, 'flag' => 'CTF{magic_bytes_png_89504e47}'],
                ['title' => 'PCAP Analysis', 'desc' => 'We captured some network traffic. Find the hidden flag being exfiltrated via HTTP.', 'difficulty' => ChallengeLevel::Medium, 'score' => 250, 'flag' => 'CTF{wireshark_http_export}'],
                ['title' => 'Memory Dump', 'desc' => 'Analyze this Windows memory dump and extract the user\'s NTLM hash.', 'difficulty' => ChallengeLevel::Hard, 'score' => 500, 'flag' => 'CTF{volatility_mimikatz_dump}'],
            ],
            'Miscellaneous' => [
                ['title' => 'Discord Welcome', 'desc' => 'Join our Discord server and read the #rules channel.', 'difficulty' => ChallengeLevel::Easy, 'score' => 100, 'flag' => 'CTF{welcome_to_undiksha_ctf}'],
                ['title' => 'Regex Crossword', 'desc' => 'Solve the regular expression crossword puzzle to get the flag.', 'difficulty' => ChallengeLevel::Medium, 'score' => 250, 'flag' => 'CTF{regex_master_ninja}'],
                ['title' => 'OSINT Twitter', 'desc' => 'Find the flag hidden in one of the tweets of the target user.', 'difficulty' => ChallengeLevel::Hard, 'score' => 500, 'flag' => 'CTF{osint_twitter_detective}'],
            ]
        ];

        foreach ($events as $event) {
            // Create Challenges for Event across all categories
            $challenges = collect();
            
            foreach ($categories as $category) {
                $catChallenges = $realChallenges[$category->name] ?? [];
                
                foreach ($catChallenges as $cData) {
                    $challenge = Challenge::create([
                        'event_id' => $event->id,
                        'category_id' => $category->id,
                        'title' => $cData['title'],
                        'description' => $cData['desc'],
                        'base_score' => $cData['score'],
                        'difficulty' => $cData['difficulty'],
                        'flag' => $cData['flag'],
                        'is_active' => true,
                    ]);

                    // Situation: Add an attachment to some challenges (approx 50% chance)
                    if (rand(0, 1)) {
                        $fileName = 'challenge_file_'.Str::random(4).'.txt';
                        $filePath = 'challenge-attachments/'.Str::random(10).'.txt';

                        // Create a REAL physical file in the local disk (storage/app/private/challenge-attachments)
                        Storage::disk('local')->put($filePath, 'This is a dummy file for '.$challenge->title);

                        ChallengeAttachment::create([
                            'challenge_id' => $challenge->id,
                            'file_name' => $fileName,
                            'file_path' => $filePath,
                            'download_count' => rand(0, 100),
                        ]);
                    }

                    $challenges->push($challenge);
                }

                // Situation: Create an inactive challenge (hidden from users)
                Challenge::factory()->create([
                    'event_id' => $event->id,
                    'category_id' => $category->id,
                    'is_active' => false,
                    'difficulty' => ChallengeLevel::VeryHard,
                    'title' => 'Inactive '.$category->name.' Challenge',
                ]);
            }

            // Create Teams
            $teams = Team::factory(10)->create([
                'event_id' => $event->id,
            ]);

            // Add our specific testers to the first 3 teams
            $teams[0]->users()->syncWithoutDetaching([$tester1->id, $tester2->id]);
            $teams[1]->users()->syncWithoutDetaching([$tester3->id]);

            // Special Team Situations
            $teamZeroSubmissions = $teams[2]; // Situation: Team with 0 submissions
            $teamOnlyIncorrect = $teams[3]; // Situation: Team with only incorrect submissions

            // Seed other random users for teams and simulate submissions
            foreach ($teams as $team) {
                // If team has no users, create some
                if ($team->users()->count() === 0) {
                    $teamUsers = User::factory(rand(1, 3))->create();
                    $team->users()->attach($teamUsers->pluck('id'));
                }

                $teamUsers = $team->users()->get();

                // Situation: Team with 0 submissions
                if ($team->id === $teamZeroSubmissions->id) {
                    continue;
                }

                // Situation: Team with ONLY incorrect submissions
                if ($team->id === $teamOnlyIncorrect->id) {
                    $challenge = $challenges->random();
                    $user = $teamUsers->random();
                    $submitTime = fake()->dateTimeBetween($event->start_time, $event->end_time < now() ? $event->end_time : now());

                    Submission::factory()->incorrect()->create([
                        'user_id' => $user->id,
                        'team_id' => $team->id,
                        'challenge_id' => $challenge->id,
                        'submitted_flag' => 'CTF{wrong_flag_'.Str::random(5).'}',
                        'points_awarded' => -1 * (int) floor($challenge->base_score * 0.05),
                        'created_at' => $submitTime,
                    ]);

                    continue; // skip creating correct submissions
                }

                // Normal Teams: Generate random submissions
                $solvedChallenges = $challenges->random(rand(2, 6)); // Each team solves 2-6 challenges

                // Track solve count per challenge to compute correct points
                if (!isset($challengeSolveCount)) {
                    $challengeSolveCount = [];
                }

                foreach ($solvedChallenges as $challenge) {
                    $user = $teamUsers->random(); // Random member submits

                    $submitTime = fake()->dateTimeBetween($event->start_time, $event->end_time < now() ? $event->end_time : now());

                    // 1-3 incorrect submissions before correct one
                    $incorrectCount = rand(0, 3);
                    for ($i = 0; $i < $incorrectCount; $i++) {
                        Submission::factory()->incorrect()->create([
                            'user_id' => $user->id,
                            'team_id' => $team->id,
                            'challenge_id' => $challenge->id,
                            'submitted_flag' => 'CTF{wrong_flag_'.Str::random(5).'}',
                            'points_awarded' => -1 * (int) floor($challenge->base_score * 0.05),
                            'created_at' => (clone $submitTime)->modify('-'.rand(1, 60).' minutes'),
                        ]);
                    }

                    // Calculate points: first solver gets full, 2nd+ get 90%
                    $solverIndex = $challengeSolveCount[$challenge->id] ?? 0;
                    $points = $solverIndex === 0
                        ? $challenge->base_score
                        : (int) floor($challenge->base_score * 0.90);
                    $challengeSolveCount[$challenge->id] = $solverIndex + 1;

                    // 1 correct submission
                    Submission::factory()->correct()->create([
                        'user_id' => $user->id,
                        'team_id' => $team->id,
                        'challenge_id' => $challenge->id,
                        'submitted_flag' => $challenge->flag,
                        'points_awarded' => $points,
                        'created_at' => $submitTime,
                    ]);
                }
            }
        }

        // Situation: A team that joins the upcoming event
        $upcomingTeam = Team::factory()->create([
            'event_id' => $upcomingEvent->id,
            'name' => 'Early Birds Team',
        ]);
        $upcomingTeam->users()->syncWithoutDetaching([$tester1->id]);
    }
}
