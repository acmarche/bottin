<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

final class RegenerateApiTokenAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Régénérer le token API')
            ->icon(Heroicon::ArrowPath)
            ->visible(fn (User $record): bool => $record->api_token !== null)
            ->requiresConfirmation()
            ->modalDescription('L\'ancien token API sera remplacé par un nouveau.')
            ->color('warning')
            ->action(function (User $record): void {
                $record->update(['api_token' => Str::random(60)]);

                Notification::make()
                    ->title('Token API régénéré')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'regenerateApiToken';
    }
}
