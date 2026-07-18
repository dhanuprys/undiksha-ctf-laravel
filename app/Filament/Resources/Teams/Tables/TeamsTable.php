<?php

namespace App\Filament\Resources\Teams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Tim')
                    ->searchable(),
                TextColumn::make('event.name')
                    ->label('Acara')
                    ->sortable(),
                TextColumn::make('join_code')
                    ->label('Kode Bergabung')
                    ->copyable()
                    ->badge()
                    ->color('gray')
                    ->fontFamily('mono'),
                TextColumn::make('users_count')
                    ->label('Jumlah Anggota')
                    ->counts('users')
                    ->badge(),
                TextColumn::make('total_score')
                    ->label('Total Skor')
                    ->badge()
                    ->color('success'),
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
