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
            $currentTeam = $user->teams()
                ->where('event_id', $activeEvent->id)
                ->withSum('submissions as total_score', 'points_awarded')
                ->first();

            if ($currentTeam) {
                // Get basic team stats
                $stats['solved_count'] = $currentTeam->submissions()->where('is_correct', true)->count();
                $stats['total_points'] = (int) ($currentTeam->total_score ?? 0);

                // Calculate rank based on total score of all teams in this event
                // Higher score is better. Tie breaker is earliest last correct submission.
                $teamsInEvent = $activeEvent->teams()
                    ->withSum('submissions as total_score_calc', 'points_awarded')
                    ->withMax(['submissions as last_solve_time' => function ($query) {
                        $query->where('is_correct', true);
                    }], 'created_at')
                    ->get()
                    ->map(function ($team) {
                        $team->total_score_calc = $team->total_score_calc ?? 0;

                        return $team;
                    })
                    ->sort(function ($a, $b) {
                        if ($a->total_score_calc !== $b->total_score_calc) {
                            return $b->total_score_calc <=> $a->total_score_calc;
                        }
                        $aTime = $a->last_solve_time ?? PHP_INT_MAX;
                        $bTime = $b->last_solve_time ?? PHP_INT_MAX;

                        return $aTime <=> $bTime;
                    })
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
