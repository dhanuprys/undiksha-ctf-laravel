<?php

namespace App\Filament\Resources\Challenges\Tables;

use App\Enums\ChallengeLevel;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ChallengesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                TextColumn::make('event.name')
                    ->label('Acara')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),
                TextColumn::make('difficulty')
                    ->label('Tingkat Kesulitan')
                    ->badge(),
                TextColumn::make('base_score')
                    ->label('Skor Dasar')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('correct_submissions_count')
                    ->label('Diselesaikan Oleh')
                    ->counts([
                        'submissions as correct_submissions_count' => fn ($query) => $query->where('is_correct', true),
                    ])
                    ->badge(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label('Acara')
                    ->relationship('event', 'name'),
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                SelectFilter::make('difficulty')
                    ->label('Tingkat Kesulitan')
                    ->options(ChallengeLevel::class),
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->slideOver()
                    ->label('Lihat Detail'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
