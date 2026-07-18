<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Submission;
use App\Models\Team;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class EventMonitorChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 1;

    protected ?string $heading = 'Perkembangan Poin Tim Teratas';

    protected ?string $pollingInterval = '10s';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $eventId = $this->filters['eventId'] ?? null;

        if (! $eventId) {
            return ['datasets' => [], 'labels' => []];
        }

        $activeEvent = Event::find($eventId);
        if (! $activeEvent) {
            return ['datasets' => [], 'labels' => []];
        }

        // Get top 10 teams
        $topTeams = Team::query()
            ->where('event_id', $eventId)
            ->withSum(['submissions as score' => function ($query) {
                $query->where('is_correct', true);
            }], 'points_awarded')
            ->withMax(['submissions as last_solve_time' => function ($query) {
                $query->where('is_correct', true);
            }], 'created_at')
            ->orderByRaw('COALESCE(score, 0) DESC')
            ->orderBy('last_solve_time', 'ASC')
            ->limit(10)
            ->get();

        if ($topTeams->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $topTeamIds = $topTeams->pluck('id')->toArray();
        $submissions = Submission::whereIn('team_id', $topTeamIds)
            ->where('is_correct', true)
            ->orderBy('created_at', 'asc')
            ->get();

        // Create labels from the start of the event until now, or just use all unique submission timestamps
        $labels = [];
        $uniqueTimestamps = $submissions->pluck('created_at')->map(fn ($date) => $date->format('H:i'))->unique()->values()->toArray();

        $datasets = [];

        $colors = [
            '#ef4444', '#f97316', '#f59e0b', '#84cc16', '#22c55e',
            '#06b6d4', '#3b82f6', '#6366f1', '#a855f7', '#ec4899',
        ];

        foreach ($topTeams as $index => $team) {
            $teamSubmissions = $submissions->where('team_id', $team->id);
            $cumulativeScore = 0;

            $dataPoints = [];
            foreach ($uniqueTimestamps as $timestamp) {
                // Find if this team had a submission at or before this timestamp
                $scoreAtTime = $teamSubmissions->filter(function ($sub) use ($timestamp) {
                    return $sub->created_at->format('H:i') <= $timestamp;
                })->sum('points_awarded');

                $dataPoints[] = $scoreAtTime;
            }

            $datasets[] = [
                'label' => $team->name,
                'data' => $dataPoints,
                'borderColor' => $colors[$index % count($colors)],
                'fill' => false,
                'tension' => 0.1,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $uniqueTimestamps,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
