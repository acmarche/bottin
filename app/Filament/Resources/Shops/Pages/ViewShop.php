<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Pages;

use App\Actions\ReminderAction;
use App\Filament\Resources\Shops\Schemas\ShopInfolist;
use App\Filament\Resources\Shops\ShopResource;
use App\Models\Shop;
use App\Models\Token;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Illuminate\Support\Str;

final class ViewShop extends ViewRecord
{
    protected static string $resource = ShopResource::class;

    public function getTitle(): string
    {
        return $this->record->company ?? 'Empty name';
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Callout::make('Cette fiche n\'a aucune catégorie associée.')
                    ->warning()
                    ->visible(fn (): bool => $this->record->categories()->doesntExist()),
                $this->hasInfolist()
                    ? $this->getInfolistContentComponent()
                    : $this->getFormContentComponent(),
                $this->getRelationManagersContentComponent(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return ShopInfolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('tabler-edit'),
            ActionGroup::make([
                Action::make('rapport')
                    ->label('Export en pdf')
                    ->icon('tabler-pdf')
                    ->url(fn (Shop $record) => route('export.shop', $record))
                    ->action(function () {
                        Notification::make()
                            ->title('Pdf exporté')
                            ->success()
                            ->send();
                    }),
                ReminderAction::createAction($this->record),
                Action::make('generateToken')
                    ->label('Générer un token')
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
                    }),
                Action::make('regenerateToken')
                    ->label('Régénérer le token')
                    ->icon('tabler-refresh')
                    ->visible(fn (Shop $record): bool => $record->token !== null)
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
                    }),
                Action::make('deleteToken')
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
                    }),
            ])
                ->label('Autres actions')
                ->button()
                ->size(Size::Large)
                ->color('secondary'),
            Actions\DeleteAction::make()
                ->icon('tabler-trash'),
        ];
    }
}
