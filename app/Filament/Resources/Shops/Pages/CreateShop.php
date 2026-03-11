<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Pages;

use App\Filament\Resources\Shops\Schemas\ShopForm;
use App\Filament\Resources\Shops\ShopResource;
use App\Models\Shop;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;

final class CreateShop extends Page
{
    public string $company = '';

    /** @var Collection<int, Shop> */
    public Collection $results;

    protected string $view = 'filament.resources.shops.pages.create-shop';

    protected static string $resource = ShopResource::class;

    public function mount(): void
    {
        $this->results = new Collection();
    }

    public function getTitle(): string
    {
        return 'Ajouter une fiche';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Veuillez vérifier que la fiche n\'existe pas déjà';
    }

    public function form(Schema $schema): Schema
    {
        return ShopForm::toCreate($schema);
    }

    public function create(): void
    {
        $company = mb_trim($this->company);

        if ($company === '') {
            return;
        }

        $shop = Shop::create(['company' => $company]);

        $this->redirect(ShopResource::getUrl('edit', ['record' => $shop]));
    }
}
