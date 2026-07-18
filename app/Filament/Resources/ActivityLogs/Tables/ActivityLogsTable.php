<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivityLogsTable
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
                TextColumn::make('log_name')
                    ->label('Nama Log')
                    ->badge(),
                TextColumn::make('description')
                    ->label('Deskripsi'),
                TextColumn::make('subject_type')
                    ->label('Subjek')
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                TextColumn::make('subject_id')
                    ->label('ID'),
                TextColumn::make('causer.name')
                    ->label('Aktor'),
                TextColumn::make('properties')
                    ->label('Data')
                    ->formatStateUsing(fn (string $state): string => 'Lihat Data')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                // We'll keep filters simple for now
            ])
            ->recordActions([
                // Read-only
            ])
            ->toolbarActions([
                // Read-only
            ]);
    }
}
