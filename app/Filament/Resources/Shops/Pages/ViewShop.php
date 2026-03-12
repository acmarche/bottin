<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Pages;

use App\Actions\ReminderAction;
use App\Filament\Actions\DeleteTokenAction;
use App\Filament\Actions\ExportPdfAction;
use App\Filament\Actions\GenerateTokenAction;
use App\Filament\Actions\RegenerateTokenAction;
use App\Filament\Resources\Shops\Schemas\ShopInfolist;
use App\Filament\Resources\Shops\ShopResource;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;

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
                    ->visible(fn(): bool => $this->record->categories()->doesntExist()),
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
                ExportPdfAction::make(),
                ReminderAction::createAction($this->record),
                GenerateTokenAction::make(),
                RegenerateTokenAction::make(),
                DeleteTokenAction::make(),
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
