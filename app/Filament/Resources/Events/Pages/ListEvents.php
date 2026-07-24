<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Catatan: Hanya boleh ada satu acara (event) yang aktif dalam satu waktu. Jika Anda mengaktifkan acara baru, acara yang sedang aktif sebelumnya akan dinonaktifkan secara otomatis.';
    }
}
