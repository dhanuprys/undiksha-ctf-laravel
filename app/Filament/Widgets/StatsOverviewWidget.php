<?php

namespace App\Filament\Widgets;

use App\Models\Challenge;
use App\Models\Event;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeEventsCount = Event::where('is_active', true)->count();
        $totalEvents = Event::count();

        $activeChallengesCount = Challenge::where('is_active', true)->count();
        $totalChallenges = Challenge::count();

        $totalSubmissions = Submission::count();
        $correctSubmissions = Submission::where('is_correct', true)->count();
        $correctRate = $totalSubmissions > 0 ? round(($correctSubmissions / $totalSubmissions) * 100) : 0;

        return [
            Stat::make('Total Acara', $totalEvents)
                ->description("{$activeEventsCount} aktif")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Total Tim', Team::count())
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Total Peserta', User::where('is_admin', false)->count())
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Total Tantangan', $totalChallenges)
                ->description("{$activeChallengesCount} aktif")
                ->descriptionIcon('heroicon-m-puzzle-piece')
                ->color('warning'),

            Stat::make('Total Submisi', $totalSubmissions)
                ->description("{$correctRate}% benar")
                ->descriptionIcon('heroicon-m-document-check')
                ->color($correctRate > 50 ? 'success' : 'danger'),
        ];
    }
}
