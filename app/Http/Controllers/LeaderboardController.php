<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Submission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $activeEvent = Event::where('is_active', true)->first();

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
        $teams = $activeEvent->teams()
            ->withCount(['submissions as solved_count' => function ($query) {
                $query->where('is_correct', true);
            }])
            ->withSum(['submissions as score' => function ($query) {
                $query->where('is_correct', true);
            }], 'points_awarded')
            ->withMax(['submissions as last_solve_time' => function ($query) {
                $query->where('is_correct', true);
            }], 'created_at')
            ->get()
            ->map(function ($team) {
                // Ensure null values are treated as 0 for sorting
                $team->score = $team->score ?? 0;

                return $team;
            })
            ->sortByDesc('score')
            ->sortBy('last_solve_time', SORT_REGULAR, true)
            ->values();

        $leaderboard = $teams->map(function ($team, $index) use ($currentTeamId) {
            return [
                'rank' => $index + 1,
                'team' => [
                    'id' => $team->id,
                    'name' => $team->name,
                ],
                'total_score' => $team->score,
                'solved_count' => $team->solved_count,
                'last_solve_time' => $team->last_solve_time ? \Carbon\Carbon::parse($team->last_solve_time)->toIso8601String() : null,
                'is_current_team' => $team->id === $currentTeamId,
            ];
        });

        $top10Teams = collect($leaderboard)->take(10);
        $top10TeamIds = $top10Teams->pluck('team.id');

        $submissions = Submission::whereIn('team_id', $top10TeamIds)
            ->where('is_correct', true)
            ->orderBy('created_at')
            ->get();

        $eventStartTime = $activeEvent->start_time ? \Carbon\Carbon::parse($activeEvent->start_time)->toIso8601String() : null;

        // Predefined distinct colors for top 10 teams
        $colors = [
            '#ef4444', '#f97316', '#f59e0b', '#84cc16', '#22c55e', 
            '#06b6d4', '#3b82f6', '#6366f1', '#a855f7', '#ec4899'
        ];

        $graphData = [];
        
        foreach ($top10Teams as $index => $entry) {
            $teamId = $entry['team']['id'];
            $teamSubmissions = $submissions->where('team_id', $teamId);
            
            $dataPoints = [];
            
            if ($eventStartTime) {
                $dataPoints[] = [
                    'x' => $eventStartTime,
                    'y' => 0,
                ];
            }

            $cumulativeScore = 0;
            foreach ($teamSubmissions as $sub) {
                $cumulativeScore += $sub->points_awarded;
                $dataPoints[] = [
                    'x' => $sub->created_at->toIso8601String(),
                    'y' => $cumulativeScore,
                ];
            }

            if (!empty($dataPoints)) {
                $dataPoints[] = [
                    'x' => now()->toIso8601String(),
                    'y' => $cumulativeScore,
                ];
            }

            $graphData[] = [
                'team_id' => $teamId,
                'team_name' => $entry['team']['name'],
                'color' => $colors[$index % count($colors)],
                'data' => $dataPoints,
            ];
        }

        return Inertia::render('competition/Leaderboard', [
            'leaderboard' => $leaderboard,
            'graphData' => $graphData,
            'status' => 'active',
        ]);
    }
}
