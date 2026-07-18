<?php

namespace App\Filament\Resources\Submissions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Peserta')
                            ->columnSpan(1)
                            ->schema([
                                Select::make('user_id')
                                    ->label('Peserta')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('team_id')
                                    ->label('Tim')
                                    ->relationship('team', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                        Section::make('Tantangan')
                            ->columnSpan(1)
                            ->schema([
                                Select::make('challenge_id')
                                    ->label('Tantangan')
                                    ->relationship('challenge', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('points_awarded')
                                    ->label('Poin Diberikan')
                                    ->required()
                                    ->numeric(),
                            ]),
                        Section::make('Jawaban')
                            ->columnSpan('full')
                            ->schema([
                                TextInput::make('submitted_flag')
                                    ->label('Bendera yang Dikumpulkan')
                                    ->required(),
                                Toggle::make('is_correct')
                                    ->label('Status Benar')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
