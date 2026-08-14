<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $activeEvent = Event::getActiveEvent();

        if (! $activeEvent) {
            return Inertia::render('competition/Leaderboard', [
                'leaderboard' => [],
                'status' => 'no_event',
            ]);
        }

        $currentTeamId = null;
        if ($user) {
            $currentTeam = $user->teams()->where('event_id', $activeEvent->id)->first();
            if ($currentTeam) {
                $currentTeamId = $currentTeam->id;
            }
        }

        // Teams leaderboard logic
        $leaderboardService = app(LeaderboardService::class);
        $cachedData = $leaderboardService->getCachedLeaderboard($activeEvent);

        $leaderboard = collect($cachedData['leaderboard'])->map(function ($entry) use ($currentTeamId) {
            $entry['is_current_team'] = $entry['team']['id'] === $currentTeamId;

            return $entry;
        })->toArray();

        $graphData = $cachedData['graphData'];

        return Inertia::render('competition/Leaderboard', [
            'leaderboard' => $leaderboard,
            'graphData' => $graphData,
            'status' => 'active',
        ]);
    }
}
