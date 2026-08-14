<?php

use App\Models\Category;
use App\Models\Challenge;
use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    $this->event = Event::factory()->create([
        'is_active' => true,
        'start_time' => now()->subDay(),
        'end_time' => now()->addDay(),
    ]);

    $this->category = Category::factory()->create();

    $this->challenge = Challenge::factory()->create([
        'event_id' => $this->event->id,
        'category_id' => $this->category->id,
        'base_score' => 100,
        'flag' => 'CTF{test_flag_123}',
        'is_active' => true,
    ]);

    $this->user = User::factory()->create();
    $this->team = Team::factory()->create([
        'event_id' => $this->event->id,
    ]);

    // Attach user to team
    $this->team->users()->attach($this->user->id);
});

it('can successfully submit a correct flag and receive points', function () {
    $this->actingAs($this->user)
        ->post(route('submissions.store'), [
            'challenge_id' => $this->challenge->id,
            'flag' => 'CTF{test_flag_123}',
        ])
        ->assertSessionHas('flash.type', 'success')
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('submissions', [
        'team_id' => $this->team->id,
        'challenge_id' => $this->challenge->id,
        'is_correct' => true,
        'submitted_flag' => 'CTF{test_flag_123}',
    ]);
});

it('is exactly case sensitive', function () {
    $this->actingAs($this->user)
        ->post(route('submissions.store'), [
            'challenge_id' => $this->challenge->id,
            'flag' => 'ctf{test_flag_123}', // lowercase
        ])
        ->assertSessionHasErrors('flag');

    $this->assertDatabaseHas('submissions', [
        'team_id' => $this->team->id,
        'challenge_id' => $this->challenge->id,
        'is_correct' => false,
        'submitted_flag' => 'ctf{test_flag_123}',
    ]);
});

it('prevents multiple correct submissions from the same team', function () {
    // First correct submission
    $this->actingAs($this->user)
        ->post(route('submissions.store'), [
            'challenge_id' => $this->challenge->id,
            'flag' => 'CTF{test_flag_123}',
        ]);

    // Second correct submission
    $this->actingAs($this->user)
        ->post(route('submissions.store'), [
            'challenge_id' => $this->challenge->id,
            'flag' => 'CTF{test_flag_123}',
        ])
        ->assertSessionHas('flash.type', 'warning')
        ->assertSessionHas('flash.message', 'Tantangan ini sudah diselesaikan oleh tim Anda.');
});

it('applies rate limiting on submissions', function () {
    // Reset rate limiter for the current request
    RateLimiter::clear('submissions.store');

    for ($i = 0; $i < 10; $i++) {
        $this->actingAs($this->user)
            ->post(route('submissions.store'), [
                'challenge_id' => $this->challenge->id,
                'flag' => 'wrong_flag_'.$i,
            ]);
    }

    // 11th request should be throttled (Too Many Requests - 429)
    $this->actingAs($this->user)
        ->post(route('submissions.store'), [
            'challenge_id' => $this->challenge->id,
            'flag' => 'wrong_flag_11',
        ])
        ->assertStatus(429);
});
