<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $activeEvent = Event::where('is_active', true)->first();

        $currentTeam = null;
        $stats = [
            'solved_count' => 0,
            'total_points' => 0,
            'rank' => '-',
            'total_challenges' => $activeEvent ? $activeEvent->challenges()->active()->count() : 0,
        ];
        $recentSubmissions = [];

        if ($activeEvent && $user) {
            $currentTeam = $user->teams()->where('event_id', $activeEvent->id)->first();

            if ($currentTeam) {
                // Get basic team stats
                $stats['solved_count'] = $currentTeam->submissions()->where('is_correct', true)->count();
                $stats['total_points'] = $currentTeam->total_score;

                // Calculate rank based on total score of all teams in this event
                // Higher score is better. Tie breaker is earliest last correct submission.
                $teamsInEvent = $activeEvent->teams()
                    ->withSum(['submissions as total_score_calc' => function ($query) {
                        $query->where('is_correct', true);
                    }], 'points_awarded')
                    ->withMax(['submissions as last_solve_time' => function ($query) {
                        $query->where('is_correct', true);
                    }], 'created_at')
                    ->get()
                    ->sortByDesc('total_score_calc')
                    ->sortBy('last_solve_time', SORT_REGULAR, true) // if same score, sort by oldest time first
                    ->values();

                $rankIndex = $teamsInEvent->search(function ($team) use ($currentTeam) {
                    return $team->id === $currentTeam->id;
                });

                if ($rankIndex !== false) {
                    $stats['rank'] = $rankIndex + 1;
                }

                $recentSubmissions = $currentTeam->submissions()
                    ->where('is_correct', true)
                    ->with('challenge:id,title,category_id', 'challenge.category:id,name')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            }
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentSubmissions' => $recentSubmissions,
        ]);
    }
}
