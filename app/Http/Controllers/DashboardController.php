<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $activeEvent = Event::getActiveEvent();

        $currentTeam = null;
        $stats = [
            'solved_count' => 0,
            'total_points' => 0,
            'rank' => '-',
            'total_challenges' => $activeEvent ? $activeEvent->challenges()->active()->count() : 0,
        ];
        $recentSubmissions = [];

        if ($activeEvent && $user) {
            $currentTeam = $user->teams()
                ->where('event_id', $activeEvent->id)
                ->withSum('submissions as total_score', 'points_awarded')
                ->first();

            if ($currentTeam) {
                // Get basic team stats
                $stats['solved_count'] = $currentTeam->submissions()->where('is_correct', true)->count();
                $stats['total_points'] = (int) ($currentTeam->total_score ?? 0);

                // Calculate rank based on cached leaderboard
                $leaderboardService = app(LeaderboardService::class);
                $cachedLeaderboard = $leaderboardService->getCachedLeaderboard($activeEvent);
                $leaderboardArray = $cachedLeaderboard['leaderboard'];

                $teamRankData = collect($leaderboardArray)->firstWhere('team.id', $currentTeam->id);
                if ($teamRankData) {
                    $stats['rank'] = $teamRankData['rank'];
                }

                $recentSubmissions = $currentTeam->submissions()
                    ->with(['challenge:id,title,category_id', 'challenge.category:id,name', 'user:id,name'])
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
            }
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentSubmissions' => $recentSubmissions,
        ]);
    }
}
