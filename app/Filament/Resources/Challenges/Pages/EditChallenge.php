<?php

namespace App\Filament\Resources\Challenges\Pages;

use App\Filament\Resources\Challenges\ChallengeResource;
use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditChallenge extends EditRecord
{
    protected static string $resource = ChallengeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reset_penalties')
                ->label('Reset Penalties')
                ->color('warning')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalDescription('Are you sure you want to reset all negative penalty points to 0 for this challenge? This action will instantly update the leaderboard.')
                ->action(function () {
                    $challenge = $this->getRecord();

                    $updatedCount = Submission::where('challenge_id', $challenge->id)
                        ->where('points_awarded', '<', 0)
                        ->update(['points_awarded' => 0]);

                    if ($updatedCount > 0) {
                        Cache::forget('event_'.$challenge->event_id.'_categories_challenges');
                        Cache::forget('leaderboard_event_'.$challenge->event_id);
                    }
                })
                ->successNotificationTitle('Penalties reset successfully'),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
