<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Team;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LeaderboardWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '15s';

    public function table(Table $table): Table
    {
        $activeEvent = Event::where('is_active', true)->first();

        return $table
            ->query(
                Team::query()
                    ->when($activeEvent, fn (Builder $query) => $query->where('event_id', $activeEvent->id))
                    ->withSum('submissions as total_score', 'points_awarded')
                    ->withCount(['submissions as challenges_solved' => fn (Builder $query) => $query->where('is_correct', true)])
                    ->withMax('submissions as last_submission_at', 'created_at')
                    ->orderByDesc('total_score')
                    ->limit(10)
            )
            ->heading($activeEvent ? "Papan Peringkat - {$activeEvent->name}" : 'Papan Peringkat')
            ->description('Top 10 tim untuk acara aktif saat ini.')
            ->columns([
                Tables\Columns\TextColumn::make('rank')
                    ->label('#')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tim')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_score')
                    ->label('Total Skor')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('challenges_solved')
                    ->label('Diselesaikan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_submission_at')
                    ->label('Submisi Terakhir')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
