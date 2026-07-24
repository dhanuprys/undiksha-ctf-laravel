<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $event = $this->getRecord();

        $data['degradation_rate'] = $event->settings()->where('key', 'degradation_rate')->value('value');
        $data['penalty_deduction'] = $event->settings()->where('key', 'penalty_deduction')->value('value');
        $data['max_team_size'] = $event->settings()->where('key', 'max_team_size')->value('value') ?? 3;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove settings fields from data so it doesn't try to save them to the event table
        unset($data['degradation_rate']);
        unset($data['penalty_deduction']);
        unset($data['max_team_size']);

        return $data;
    }

    protected function afterSave(): void
    {
        $event = $this->getRecord();
        $formData = $this->form->getState();

        $settingsKeys = ['degradation_rate', 'penalty_deduction', 'max_team_size'];

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
