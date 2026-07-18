<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class ChallengeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $activeEvent = Event::where('is_active', true)->first();

        if (! $activeEvent || ! $activeEvent->start_time || now()->lt($activeEvent->start_time)) {
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

        // Transform data to include solved_by_team flag and format difficulty
        $categories->transform(function ($category) use ($solvedChallengeIds) {
            $category->challenges->transform(function ($challenge) use ($solvedChallengeIds) {
                $challenge->solved_by_team = in_array($challenge->id, $solvedChallengeIds);
                // HTML sanitize description for safe rendering
                $challenge->description = strip_tags($challenge->description, '<p><br><b><strong><i><em><u><ul><ol><li><a><code><pre><blockquote><h1><h2><h3><h4><h5><h6>');

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

        $challenge->description = strip_tags($challenge->description, '<p><br><b><strong><i><em><u><ul><ol><li><a><code><pre><blockquote><h1><h2><h3><h4><h5><h6>');
        $challenge->solve_count = $challenge->submissions()->where('is_correct', true)->count();
        $challenge->solved_by_team = $currentTeam->submissions()
            ->where('challenge_id', $challenge->id)
            ->where('is_correct', true)
            ->exists();

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
