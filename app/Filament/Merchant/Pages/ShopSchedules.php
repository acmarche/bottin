<?php

declare(strict_types=1);

namespace App\Filament\Merchant\Pages;

use App\Livewire\Merchant\ShopSchedulesTable;
use App\Models\Shop;
use App\Models\Token;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

final class ShopSchedules extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Horaires';

    protected static ?string $title = 'Horaires';

    protected static ?int $navigationSort = 4;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(ShopSchedulesTable::class, [
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
