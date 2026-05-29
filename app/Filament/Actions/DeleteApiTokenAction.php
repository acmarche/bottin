<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

final class DeleteApiTokenAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Supprimer le token API')
            ->icon('tabler-trash')
            ->visible(fn (User $record): bool => $record->api_token !== null)
            ->requiresConfirmation()
            ->modalDescription('Le token API sera définitivement supprimé.')
            ->color('danger')
            ->action(function (User $record): void {
                $record->update(['api_token' => null]);

                Notification::make()
                    ->title('Token API supprimé')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'deleteApiToken';
    }
}
