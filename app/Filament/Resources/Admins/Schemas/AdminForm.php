<?php

namespace App\Filament\Resources\Admins\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengguna')
                    ->icon('heroicon-o-user')
                    ->columnSpan('full')
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
                            TextInput::make('password')
                                ->label('Kata Sandi (Kosongkan jika tidak ingin mengubah)')
                                ->password()
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->dehydrated(fn ($state) => filled($state))
                                ->maxLength(255)
                                ->confirmed(),
                            TextInput::make('password_confirmation')
                                ->label('Konfirmasi Kata Sandi')
                                ->password()
                                ->requiredWith('password')
                                ->dehydrated(false),
                        ]),
                    ]),
            ]);
    }
}
