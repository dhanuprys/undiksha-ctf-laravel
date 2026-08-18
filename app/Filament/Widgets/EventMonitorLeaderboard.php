<?php

namespace App\Filament\Widgets;

use App\Models\Team;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class EventMonitorLeaderboard extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '10s';

    protected static ?string $heading = 'Leaderboard';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $eventId = $this->filters['eventId'] ?? null;

                if (! $eventId) {
                    return Team::query()->whereNull('id'); // empty query
                }

                return Team::query()
                    ->where('event_id', $eventId)
                    ->withCount(['submissions as solved_count' => function ($query) {
                        $query->where('is_correct', true);
                    }])
                    ->withSum('submissions as score', 'points_awarded')
                    ->withMax(['submissions as last_solve_time' => function ($query) {
                        $query->where('is_correct', true);
                    }], 'created_at')
                    ->orderByRaw('COALESCE(score, 0) DESC')
                    ->orderBy('last_solve_time', 'ASC');
            })
            ->columns([
                Tables\Columns\TextColumn::make('rank')
                    ->label('Rank')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tim')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('solved_count')
                    ->label('Tantangan Diselesaikan')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('score')
                    ->label('Total Poin')
                    ->weight('bold')
                    ->formatStateUsing(fn ($state) => $state ?? 0),
                Tables\Columns\TextColumn::make('last_solve_time')
                    ->label('Waktu Selesai Terakhir')
                    ->dateTime('d M Y, H:i:s')
                    ->placeholder('-'),
            ])
            ->paginated([10, 25, 50, 'all']);
    }
}
