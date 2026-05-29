<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

final class GenerateApiTokenAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Générer un token API')
            ->icon('tabler-key')
            ->visible(fn (User $record): bool => $record->api_token === null)
            ->requiresConfirmation()
            ->modalDescription('Un token API sera créé pour cet utilisateur.')
            ->action(function (User $record): void {
                $record->update(['api_token' => Str::random(60)]);

                Notification::make()
                    ->title('Token API généré')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'generateApiToken';
    }
}
