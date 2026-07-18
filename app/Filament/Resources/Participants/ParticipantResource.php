<?php

namespace App\Filament\Resources\Participants;

use App\Filament\Resources\Participants\Pages\CreateParticipant;
use App\Filament\Resources\Participants\Pages\EditParticipant;
use App\Filament\Resources\Participants\Pages\ListParticipants;
use App\Filament\Resources\Participants\RelationManagers\TeamsRelationManager;
use App\Filament\Resources\Participants\Schemas\ParticipantForm;
use App\Filament\Resources\Participants\Tables\ParticipantsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ParticipantResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Peserta';

    protected static ?string $pluralModelLabel = 'Peserta';

    protected static ?string $navigationLabel = 'Peserta';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static string|UnitEnum|null $navigationGroup = 'Peserta';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->participant();
    }

    public static function form(Schema $schema): Schema
    {
        return ParticipantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParticipantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TeamsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListParticipants::route('/'),
            'create' => CreateParticipant::route('/create'),
            'edit' => EditParticipant::route('/{record}/edit'),
        ];
    }
}
