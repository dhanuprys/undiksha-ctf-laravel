<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\Event;
use App\Services\ScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Stevebauman\Purify\Facades\Purify;

class ChallengeController extends Controller
{
    public function __construct(
        protected ScoringService $scoringService,
    ) {}

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
        $activeEvent = Event::getActiveEvent();

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
                'start_time' => $activeEvent->start_time?->toIso8601String(),
            ]);
        }

        $currentTeam = $user->teams()->where('event_id', $activeEvent->id)->first();
        if (! $currentTeam) {
            return Inertia::render('competition/Challenges', [
                'categories' => [],
                'status' => 'no_team',
            ]);
        }

        $showSolverCount = filter_var($activeEvent->getSetting('show_solver_count', true), FILTER_VALIDATE_BOOLEAN);
        $degradationRate = $this->scoringService->getDegradationRate($activeEvent->id);

        $cachedCategories = Cache::rememberForever('event_'.$activeEvent->id.'_categories_challenges', function () use ($activeEvent) {
            return Category::whereHas('challenges', function ($query) use ($activeEvent) {
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
                ->get()
                ->toArray();
        });

        $teamCorrectSubmissions = $currentTeam->submissions()
            ->where('is_correct', true)
            ->get()
            ->keyBy('challenge_id');

        $categories = array_map(function ($category) use ($teamCorrectSubmissions, $degradationRate, $showSolverCount) {
            $category['challenges'] = array_map(function ($challenge) use ($teamCorrectSubmissions, $degradationRate, $showSolverCount) {
                $teamSubmission = $teamCorrectSubmissions->get($challenge['id']);

                $challenge['solved_by_team'] = $teamSubmission !== null;
                $challenge['points_awarded'] = $teamSubmission?->points_awarded;
                $challenge['dynamic_score'] = $this->scoringService->calculateDynamicScore(
                    $challenge['base_score'],
                    $challenge['solve_count'] ?? 0,
                    $degradationRate,
                );
                $challenge['description'] = $this->sanitizeHtml($challenge['description']);

                if (! $showSolverCount) {
                    unset($challenge['solve_count']);
                }

                return $challenge;
            }, $category['challenges']);

            return $category;
        }, $cachedCategories);

        return Inertia::render('competition/Challenges', [
            'categories' => $categories,
            'status' => 'active',
        ]);
    }

    public function show(Request $request, Challenge $challenge)
    {
        $user = $request->user();
        $activeEvent = Event::getActiveEvent();

        if (! $activeEvent || $challenge->event_id !== $activeEvent->id || ! $challenge->is_active) {
            abort(404);
        }

        if (now()->lt($activeEvent->start_time)) {
            return redirect()->route('dashboard')->with('error', 'Kompetisi belum dimulai.');
        }

        if ($activeEvent->end_time && now()->gt($activeEvent->end_time)) {
            return redirect()->route('dashboard')->with('error', 'Kompetisi sudah berakhir.');
        }

        $currentTeam = $user->teams()->where('event_id', $activeEvent->id)->first();
        if (! $currentTeam) {
            return redirect()->route('dashboard')->with('error', 'Anda belum bergabung dalam tim.');
        }

        $challenge->load(['category', 'attachments']);
        $challenge->makeHidden(['flag', 'event_id', 'is_active', 'created_at', 'updated_at']);

        $challenge->description = $this->sanitizeHtml($challenge->description);

        $showSolverCount = filter_var($activeEvent->getSetting('show_solver_count', true), FILTER_VALIDATE_BOOLEAN);
        $solveCount = $challenge->submissions()->where('is_correct', true)->count();

        $challenge->solve_count = $showSolverCount ? $solveCount : null;
        $challenge->dynamic_score = $this->scoringService->calculateDynamicScore(
            $challenge->base_score,
            $solveCount,
            $this->scoringService->getDegradationRate($activeEvent->id),
        );

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
