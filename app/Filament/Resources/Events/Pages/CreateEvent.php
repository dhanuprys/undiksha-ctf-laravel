<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->action('create');
    }

    public function getSubheading(): ?string
    {
        return 'Catatan: Hanya boleh ada satu acara (event) yang aktif dalam satu waktu. Jika Anda mengaktifkan acara baru, acara yang sedang aktif sebelumnya akan dinonaktifkan secara otomatis.';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove settings fields from data so it doesn't try to save them to the event table
        unset($data['degradation_rate']);
        unset($data['penalty_deduction']);
        unset($data['max_team_size']);
        unset($data['show_solver_count']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $event = $this->getRecord();
        $formData = $this->form->getState();

        $settingsKeys = ['degradation_rate', 'penalty_deduction', 'max_team_size', 'show_solver_count'];

        foreach ($settingsKeys as $key) {
            if (array_key_exists($key, $formData)) {
                $event->settings()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $formData[$key]]
                );
            }
        }
    }
}
