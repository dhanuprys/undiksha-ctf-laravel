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

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove settings fields from data so it doesn't try to save them to the event table
        unset($data['degradation_rate']);
        unset($data['penalty_deduction']);

        return $data;
    }

    protected function afterSave(): void
    {
        $event = $this->getRecord();
        $formData = $this->form->getState();

        if (array_key_exists('degradation_rate', $formData)) {
            $event->settings()->updateOrCreate(
                ['key' => 'degradation_rate'],
                ['value' => $formData['degradation_rate']]
            );
        }

        if (array_key_exists('penalty_deduction', $formData)) {
            $event->settings()->updateOrCreate(
                ['key' => 'penalty_deduction'],
                ['value' => $formData['penalty_deduction']]
            );
        }
    }
}
