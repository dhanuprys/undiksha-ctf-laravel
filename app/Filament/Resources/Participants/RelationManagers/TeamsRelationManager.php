<?php

namespace App\Filament\Resources\Participants\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamsRelationManager extends RelationManager
{
    protected static string $relationship = 'teams';

    protected static ?string $title = 'Tim yang Diikuti';

    protected static ?string $modelLabel = 'Tim';

    protected static ?string $pluralModelLabel = 'Tim';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Tim')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Tim')
                    ->searchable(),
                TextColumn::make('event.name')
                    ->label('Acara')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Buat Tim Baru'),
                AttachAction::make()
                    ->label('Gabung ke Tim')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name'])
                    ->recordTitle(fn ($record) => "{$record->name} (".($record->event->name ?? 'Tanpa Acara').')'),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make()
                    ->label('Keluarkan'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Keluarkan Terpilih'),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
