<?php

declare(strict_types=1);

namespace App\Filament\Merchant\Pages;

use App\Livewire\Merchant\ShopMediasTable;
use App\Models\Shop;
use App\Models\Token;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

final class ShopMedias extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocument;

    protected static ?string $navigationLabel = 'Médias';

    protected static ?string $title = 'Médias';

    protected static ?int $navigationSort = 4;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(ShopMediasTable::class, [
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
