<?php

declare(strict_types=1);

namespace App\Filament\Merchant\Pages;

use App\Livewire\Merchant\ShopCategoriesTable;
use App\Models\Shop;
use App\Models\Token;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

final class ShopCategories extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Classement';

    protected static ?string $title = 'Classement';

    protected static ?int $navigationSort = 3;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(ShopCategoriesTable::class, [
                    'shopId' => $this->getShop()->id,
                ]),
            ]);
    }

    private function getShop(): Shop
    {
        /** @var Token $token */
        $token = Auth::guard('merchant')->user();

        return $token->shop;
    }
}
