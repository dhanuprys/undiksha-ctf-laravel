<?php

namespace App\Filament\Resources\Submissions\Tables;

use App\Filament\Exports\SubmissionExporter;
use App\Models\Event;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('team.name')
                    ->label('Tim')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('challenge.title')
                    ->label('Tantangan')
                    ->sortable(),
                TextColumn::make('submitted_flag')
                    ->label('Bendera yang Dikumpulkan')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_correct')
                    ->label('Benar')
                    ->boolean(),
                TextColumn::make('points_awarded')
                    ->label('Poin Diberikan')
                    ->badge()
                    ->color(fn (string $state): string => $state >= 0 ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label('Acara')
                    ->options(Event::pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $query, $value): Builder => $query->whereHas(
                            'challenge',
                            fn (Builder $query): Builder => $query->where('event_id', $value)
                        )
                    )),
                TernaryFilter::make('is_correct')
                    ->label('Status Benar'),
                SelectFilter::make('team')
                    ->label('Tim')
                    ->relationship('team', 'name')
                    ->searchable(),
                SelectFilter::make('challenge')
                    ->label('Tantangan')
                    ->relationship('challenge', 'title')
                    ->searchable(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(SubmissionExporter::class)
                    ->formats([
                        ExportFormat::Xlsx,
                    ])
                    ->label('Ekspor ke Excel (XLSX)')
                    ->modifyQueryUsing(fn (Builder $query, array $options) => $query->whereHas(
                        'challenge',
                        fn (Builder $query) => $query->where('event_id', $options['event_id'])
                    )),
            ])
            ->recordActions([
                ViewAction::make()
                    ->slideOver()
                    ->label('Lihat Detail'),
            ])
            ->toolbarActions([
                // Read-only
            ]);
    }
}
