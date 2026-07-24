<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Stevebauman\Purify\Facades\Purify;

class ChallengeController extends Controller
{
    /**
     * Sanitize HTML content using HTMLPurifier via stevebauman/purify.
     */
    protected function sanitizeHtml(?string $html): string
    {
        if (! $html) {
            return '';
        }

        return Purify::clean($html);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $activeEvent = Event::where('is_active', true)->first();

        // No active event
        if (! $activeEvent) {
            return Inertia::render('competition/Challenges', [
                'categories' => [],
                'status' => 'not_started',
            ]);
        }

        // Event has ended
        if ($activeEvent->end_time && now()->gt($activeEvent->end_time)) {
            return Inertia::render('competition/Challenges', [
                'categories' => [],
                'status' => 'ended',
            ]);
        }

        // Event has not started yet
        if (! $activeEvent->start_time || now()->lt($activeEvent->start_time)) {
            return Inertia::render('competition/Challenges', [
                'categories' => [],
                'status' => 'not_started',
            ]);
        }

        $currentTeam = $user->teams()->where('event_id', $activeEvent->id)->first();
        if (! $currentTeam) {
            return Inertia::render('competition/Challenges', [
                'categories' => [],
                'status' => 'no_team',
            ]);
        }

        // Get all categories that have active challenges in this event
        $categories = Category::whereHas('challenges', function ($query) use ($activeEvent) {
            $query->where('event_id', $activeEvent->id)->where('is_active', true);
        })
            ->with(['challenges' => function ($query) use ($activeEvent) {
                $query->where('event_id', $activeEvent->id)
                    ->where('is_active', true)
                    ->select('id', 'category_id', 'title', 'description', 'base_score', 'difficulty')
                    ->withCount(['submissions as solve_count' => function ($q) {
                        $q->where('is_correct', true);
                    }]);
            }])
            ->get();

        // Get all correct submissions for the current team to mark solved challenges
        $solvedChallengeIds = $currentTeam->submissions()
            ->where('is_correct', true)
            ->pluck('challenge_id')
            ->toArray();

        // Transform data to include solved_by_team flag and sanitize description
        $categories->transform(function ($category) use ($solvedChallengeIds) {
            $category->challenges->transform(function ($challenge) use ($solvedChallengeIds) {
                $challenge->solved_by_team = in_array($challenge->id, $solvedChallengeIds);
                $challenge->description = $this->sanitizeHtml($challenge->description);

                return $challenge;
            });

            return $category;
        });

        return Inertia::render('competition/Challenges', [
            'categories' => $categories,
            'status' => 'active',
        ]);
    }

    public function show(Request $request, Challenge $challenge)
    {
        $user = $request->user();
        $activeEvent = Event::where('is_active', true)->first();

        if (! $activeEvent || $challenge->event_id !== $activeEvent->id || ! $challenge->is_active) {
            abort(404);
        }

        if (now()->lt($activeEvent->start_time)) {
            abort(403, 'Kompetisi belum dimulai.');
        }

        $currentTeam = $user->teams()->where('event_id', $activeEvent->id)->first();
        if (! $currentTeam) {
            abort(403, 'Anda belum bergabung dalam tim.');
        }

        $challenge->load(['category', 'attachments']);

        // Hide sensitive info
        $challenge->makeHidden(['flag', 'event_id', 'is_active', 'created_at', 'updated_at']);

        $challenge->description = $this->sanitizeHtml($challenge->description);
        $challenge->solve_count = $challenge->submissions()->where('is_correct', true)->count();

        $teamCorrectSubmission = $currentTeam->submissions()
            ->where('challenge_id', $challenge->id)
            ->where('is_correct', true)
            ->first();

        $challenge->solved_by_team = $teamCorrectSubmission !== null;
        $challenge->points_awarded = $teamCorrectSubmission?->points_awarded;

        // Format attachments
        $challenge->attachments->transform(function ($attachment) {
            return [
                'id' => $attachment->id,
                'file_name' => $attachment->file_name,
                'download_url' => URL::temporarySignedRoute(
                    'attachments.download', now()->addHours(2), ['attachment' => $attachment->id]
                ),
            ];
        });

        return Inertia::render('competition/ChallengeDetail', [
            'challenge' => $challenge,
        ]);
    }
}
