<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->label('Kata Sandi (Kosongkan jika tidak ingin mengubah)')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Facades\Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255)
                    ->confirmed(),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Konfirmasi Kata Sandi')
                    ->password()
                    ->requiredWith('password')
                    ->dehydrated(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Alamat Email'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['is_admin'] = false;
                        if (empty($data['password'])) {
                            $data['password'] = \Illuminate\Support\Facades\Hash::make('password');
                        }

                        return $data;
                    }),
                AttachAction::make()
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->where('is_admin', false)),
            ])
            ->actions([
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
