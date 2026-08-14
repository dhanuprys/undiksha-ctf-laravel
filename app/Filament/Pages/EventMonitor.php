<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\EventMonitorChart;
use App\Filament\Widgets\EventMonitorLeaderboard;
use App\Models\Event;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

class EventMonitor extends Dashboard
{
    use HasFiltersForm;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationLabel = 'Monitor Acara';

    protected static ?string $title = 'Realtime Monitoring';

    protected static ?int $navigationSort = 3;

    protected static string $routePath = 'event-monitor';

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('eventId')
                    ->label('Pilih Event')
                    ->options(Event::pluck('name', 'id'))
                    ->searchable()
                    ->default(fn () => Event::getActiveEvent()?->id ?? Event::latest()->first()?->id),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            EventMonitorChart::class,
            EventMonitorLeaderboard::class,
        ];
    }
}
