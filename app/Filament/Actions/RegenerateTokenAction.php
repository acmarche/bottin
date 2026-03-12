<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\Shop;
use App\Models\Token;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

final class RegenerateTokenAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Régénérer le token')
            ->icon(Heroicon::ArrowPath)
            ->visible(fn(Shop $record): bool => $record->token !== null)
            ->requiresConfirmation()
            ->modalDescription('L\'ancien token sera supprimé et un nouveau sera créé.')
            ->color('warning')
            ->action(function (Shop $record): void {
                $record->token->delete();

                Token::create([
                    'shop_id' => $record->id,
                    'uuid' => Str::uuid()->toString(),
                    'password' => Str::random(50),
                    'expire_at' => now()->addYear(),
                ]);

                Notification::make()
                    ->title('Token régénéré')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'regenerateToken';
    }
}
