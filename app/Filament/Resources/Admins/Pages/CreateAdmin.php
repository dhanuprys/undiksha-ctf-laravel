<?php

namespace App\Filament\Resources\Admins\Pages;

use App\Filament\Resources\Admins\AdminResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_admin'] = true;
        if (empty($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make('password');
        }

        return $data;
    }
}
