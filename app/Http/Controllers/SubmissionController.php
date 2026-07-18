<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitFlagRequest;
use App\Models\Challenge;
use App\Models\Event;

class SubmissionController extends Controller
{
    public function store(SubmitFlagRequest $request)
    {
        $user = $request->user();
        $activeEvent = Event::where('is_active', true)->first();

        if (! $activeEvent) {
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Tidak ada acara yang sedang aktif.',
            ]);
        }

        if (now()->lt($activeEvent->start_time) || ($activeEvent->end_time && now()->gt($activeEvent->end_time))) {
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Waktu kompetisi sudah berakhir atau belum dimulai.',
            ]);
        }

        $currentTeam = $user->teams()->where('event_id', $activeEvent->id)->first();
        if (! $currentTeam) {
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Anda belum bergabung dalam tim.',
            ]);
        }

        $challenge = Challenge::where('id', $request->challenge_id)
            ->where('event_id', $activeEvent->id)
            ->where('is_active', true)
            ->first();

        if (! $challenge) {
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Tantangan tidak valid atau sedang tidak aktif.',
            ]);
        }

        // Check if team already solved this challenge
        $alreadySolved = $currentTeam->submissions()
            ->where('challenge_id', $challenge->id)
            ->where('is_correct', true)
            ->exists();

        if ($alreadySolved) {
            return back()->with('flash', [
                'type' => 'warning',
                'message' => 'Tim Anda sudah berhasil menyelesaikan tantangan ini.',
            ]);
        }

        $isCorrect = $request->flag === $challenge->flag;
        $pointsAwarded = $isCorrect ? $challenge->base_score : 0;

        $currentTeam->submissions()->create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'submitted_flag' => $request->flag,
            'is_correct' => $isCorrect,
            'points_awarded' => $pointsAwarded,
        ]);

        if ($isCorrect) {
            return back()->with('flash', [
                'type' => 'success',
                'message' => 'Selamat! Flag yang Anda masukkan BENAR. Tim Anda mendapatkan '.$pointsAwarded.' poin.',
            ]);
        }

        return back()->withErrors([
            'flag' => 'Flag salah, silakan coba lagi.',
        ]);
    }
}
