<?php

namespace App\Filament\Resources\Admins\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('user_tabs')
                    ->columnSpan('full')
                    ->tabs([
                        Tabs\Tab::make('Informasi Profil')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->label('Nama Lengkap')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('email')
                                        ->label('Alamat Email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255),
                                ]),
                            ]),
                        Tabs\Tab::make('Keamanan')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('password')
                                        ->label('Kata Sandi')
                                        ->password()
                                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                        ->dehydrated(fn ($state) => filled($state))
                                        ->required(fn (string $operation): bool => $operation === 'create')
                                        ->maxLength(255)
                                        ->confirmed(),
                                    TextInput::make('password_confirmation')
                                        ->label('Konfirmasi Kata Sandi')
                                        ->password()
                                        ->requiredWith('password')
                                        ->dehydrated(false),
                                ]),
                            ]),
                    ]),
            ]);
    }
}
