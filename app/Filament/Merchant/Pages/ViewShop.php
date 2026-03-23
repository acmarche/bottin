<?php

declare(strict_types=1);

namespace App\Filament\Merchant\Pages;

use App\Filament\Resources\Shops\Schemas\ShopInfolist;
use App\Models\Shop;
use App\Models\Token;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

final class ViewShop extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $navigationLabel = 'Ma fiche';

    protected static ?string $title = 'Ma fiche';

    protected static ?int $navigationSort = 1;

    public function shopInfolist(Schema $schema): Schema
    {
        return ShopInfolist::configure(
            $schema->record($this->getShop()),
        );
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Callout::make('Cette fiche n\'a aucune catégorie associée.')
                    ->warning()
                    ->visible(fn (): bool => $this->getShop()->categories()->doesntExist()),
                EmbeddedSchema::make('shopInfolist'),
            ]);
    }

    public function getTitle(): string
    {
        return $this->getShop()->company ?? 'Ma fiche';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('Modifier')
                ->icon(Heroicon::OutlinedPencilSquare)
                ->url(EditShop::getUrl()),
            Action::make('categories')
                ->label('Classements')
                ->icon(Heroicon::OutlinedTag)
                ->color('warning')
                ->url(ShopCategories::getUrl()),
            Action::make('medias')
                ->label('Médias')
                ->color('info')
                ->icon(Heroicon::OutlinedPhoto)
                ->url(ShopMedias::getUrl()),
        ];
    }

    private function getShop(): ?Shop
    {
        /** @var Token $token */
        $token = Auth::guard('merchant')->user();

        return $token->shop()->first();
    }
}
