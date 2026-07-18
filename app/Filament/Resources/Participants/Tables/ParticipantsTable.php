<?php

namespace App\Filament\Resources\Participants\Tables;

use App\Models\Event;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable(),
                TextColumn::make('teams.name')
                    ->label('Tim')
                    ->badge()
                    ->default('—'),
                TextColumn::make('submissions_count')
                    ->label('Jumlah Pengumpulan')
                    ->counts('submissions')
                    ->badge(),
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
                    ->options(Event::pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $query, $value): Builder => $query->whereHas(
                            'teams',
                            fn (Builder $query): Builder => $query->where('event_id', $value)
                        )
                    )),
                SelectFilter::make('team')
                    ->label('Tim')
                    ->relationship('teams', 'name'),
                Filter::make('has_team')
                    ->label('Memiliki Tim')
                    ->query(fn (Builder $query): Builder => $query->has('teams'))
                    ->toggle(),
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
