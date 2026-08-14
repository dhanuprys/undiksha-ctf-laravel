<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class LeaderboardService
{
    /**
     * Get the cached leaderboard data for a specific event.
     * Returns an array containing the 'leaderboard' and 'graphData'.
     */
    public function getCachedLeaderboard(Event $activeEvent): array
    {
        $cacheKey = 'leaderboard_event_'.$activeEvent->id;

        return Cache::rememberForever($cacheKey, function () use ($activeEvent) {
            return $this->calculateLeaderboard($activeEvent);
        });
    }

    /**
     * Calculate the entire leaderboard state.
     */
    protected function calculateLeaderboard(Event $activeEvent): array
    {
        // Teams leaderboard logic
        // Sum ALL submissions (including negative penalties) for total score
        $teams = $activeEvent->teams()
            ->withCount(['submissions as solved_count' => function ($query) {
                $query->where('is_correct', true);
            }])
            ->withSum('submissions as score', 'points_awarded')
            ->withMax(['submissions as last_solve_time' => function ($query) {
                $query->where('is_correct', true);
            }], 'created_at')
            ->get()
            ->map(function ($team) {
                $team->score = $team->score ?? 0;

                return $team;
            })
            ->sort(function ($a, $b) {
                // Primary: highest score first
                if ($a->score !== $b->score) {
                    return $b->score <=> $a->score;
                }
                // Tiebreaker: earliest last solve time first
                $aTime = $a->last_solve_time ? Carbon::parse($a->last_solve_time)->timestamp : PHP_INT_MAX;
                $bTime = $b->last_solve_time ? Carbon::parse($b->last_solve_time)->timestamp : PHP_INT_MAX;

                return $aTime <=> $bTime;
            })
            ->values();

        $leaderboard = $teams->map(function ($team, $index) {
            return [
                'rank' => $index + 1,
                'team' => [
                    'id' => $team->id,
                    'name' => $team->name,
                ],
                'total_score' => $team->score,
                'solved_count' => $team->solved_count,
                'last_solve_time' => $team->last_solve_time ? Carbon::parse($team->last_solve_time)->toIso8601String() : null,
            ];
        })->toArray();

        $top10Teams = collect($leaderboard)->take(10);
        $top10TeamIds = $top10Teams->pluck('team.id');

        $submissions = Submission::whereIn('team_id', $top10TeamIds)
            ->where('is_correct', true)
            ->orderBy('created_at')
            ->get();

        $eventStartTime = $activeEvent->start_time ? Carbon::parse($activeEvent->start_time)->toIso8601String() : null;

        // Predefined distinct colors for top 10 teams
        $colors = [
            '#ef4444', '#f97316', '#f59e0b', '#84cc16', '#22c55e',
            '#06b6d4', '#3b82f6', '#6366f1', '#a855f7', '#ec4899',
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

            if (! empty($dataPoints)) {
                $graphEndTime = ($activeEvent->end_time && now()->gt($activeEvent->end_time))
                    ? Carbon::parse($activeEvent->end_time)->toIso8601String()
                    : now()->toIso8601String();

                $dataPoints[] = [
                    'x' => $graphEndTime,
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

        return [
            'leaderboard' => $leaderboard,
            'graphData' => $graphData,
        ];
    }
}
