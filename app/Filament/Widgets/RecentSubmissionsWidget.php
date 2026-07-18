<?php

namespace App\Filament\Widgets;

use App\Models\Submission;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentSubmissionsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '10s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Submission::query()->latest()->limit(10)
            )
            ->heading('Submisi Terbaru')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peserta'),
                Tables\Columns\TextColumn::make('team.name')
                    ->label('Tim'),
                Tables\Columns\TextColumn::make('challenge.title')
                    ->label('Tantangan'),
                Tables\Columns\IconColumn::make('is_correct')
                    ->label('Status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('points_awarded')
                    ->label('Poin')
                    ->badge()
                    ->color(fn (string $state): string => $state >= 0 ? 'success' : 'danger'),
            ])
            ->paginated(false);
    }
}
