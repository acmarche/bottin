<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\Shop;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

final class DeleteTokenAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Supprimer le token')
            ->icon('tabler-trash')
            ->visible(fn (Shop $record): bool => $record->token !== null)
            ->requiresConfirmation()
            ->modalDescription('Le token de connexion commerçant sera définitivement supprimé.')
            ->color('danger')
            ->action(function (Shop $record): void {
                $record->token->delete();

                Notification::make()
                    ->title('Token supprimé')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'deleteToken';
    }
}
