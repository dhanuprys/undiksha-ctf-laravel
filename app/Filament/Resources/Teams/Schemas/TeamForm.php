<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Tim')
                    ->columnSpan('full')
                    ->schema([
                        Select::make('event_id')
                            ->label('Acara')
                            ->relationship('event', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama Tim')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('join_code')
                            ->label('Kode Bergabung')
                            ->readOnly()
                            ->dehydrated(false)
                            ->helperText('Join code digenerate secara otomatis ketika tim dibuat.')
                            ->hiddenOn('create'),
                    ]),
            ]);
    }
}
