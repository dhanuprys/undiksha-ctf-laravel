<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitFlagRequest;
use App\Models\Challenge;
use App\Models\Event;
use App\Services\ScoringService;
use Exception;

class SubmissionController extends Controller
{
    public function __construct(
        protected ScoringService $scoringService,
    ) {}

    public function store(SubmitFlagRequest $request)
    {
        $user = $request->user();
        $activeEvent = Event::getActiveEvent();

        if (! $activeEvent) {
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Tidak ada acara yang sedang aktif.',
            ]);
        }

        if (($activeEvent->start_time && now()->lt($activeEvent->start_time)) || ($activeEvent->end_time && now()->gt($activeEvent->end_time))) {
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

        try {
            $submission = $this->scoringService->submitFlag(
                team: $currentTeam,
                challenge: $challenge,
                userId: $user->id,
                flag: $request->flag,
            );

            if ($submission->is_correct) {
                return back()->with('flash', [
                    'type' => 'success',
                    'message' => 'Selamat! Flag yang Anda masukkan BENAR. Tim Anda mendapatkan '.$submission->points_awarded.' poin.',
                ]);
            }

            $penaltyMessage = $submission->points_awarded < 0
                ? ' Penalti: '.$submission->points_awarded.' poin.'
                : '';

            return back()->withErrors([
                'flag' => 'Flag salah, silakan coba lagi.'.$penaltyMessage,
            ]);
        } catch (Exception $e) {
            return back()->with('flash', [
                'type' => 'warning',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
