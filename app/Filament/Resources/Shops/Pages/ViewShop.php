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
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;

final class ViewShop extends ViewRecord
{
    protected static string $resource = ShopResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (request()->has('scroll')) {
            $this->js('setTimeout(() => window.scrollTo({ top: document.body.scrollHeight, behavior: "smooth" }), 300)');
        }
    }

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
            Action::make('view_front')
                ->label('Voir en front')
                ->icon(Heroicon::OutlinedEye)
                ->url(fn (): string => route('shop.show', $this->record))
                ->openUrlInNewTab(),
            Actions\EditAction::make()
                ->icon('tabler-edit'),
            ActionGroup::make([
                Action::make('scroll_to_categories')
                    ->label('Catégories')
                    ->icon('tabler-tags')->url(
                        fn (): string => ShopResource::getUrl('view', ['record' => $this->record]).'?relation=0&scroll=1'
                    ),

                Action::make('scroll_to_medias')
                    ->label('Médias')
                    ->icon('tabler-photo')->url(
                        fn (): string => ShopResource::getUrl('view', ['record' => $this->record]).'?relation=1&scroll=1'
                    ),

                Action::make('scroll_to_horaires')
                    ->label('Horaires')
                    ->icon('tabler-clock')
                    ->url(fn (): string => ShopResource::getUrl('view', ['record' => $this->record]).'?relation=2&scroll=1'),
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
