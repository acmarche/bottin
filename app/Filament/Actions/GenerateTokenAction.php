<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\Shop;
use App\Models\Token;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

final class GenerateTokenAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->label('Générer un token')
            ->icon('tabler-key')
            ->visible(fn (Shop $record): bool => $record->token === null)
            ->requiresConfirmation()
            ->modalDescription('Un token de connexion commerçant sera créé pour cette fiche.')
            ->action(function (Shop $record): void {
                Token::create([
                    'shop_id' => $record->id,
                    'uuid' => Str::uuid()->toString(),
                    'password' => Str::random(50),
                    'expire_at' => now()->addYear(),
                ]);

                Notification::make()
                    ->title('Token généré')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'generateToken';
    }
}
