<?php

declare(strict_types=1);

namespace App\Livewire\Merchant;

use App\Filament\Resources\Shops\Schemas\MediaForm;
use App\Filament\Resources\Shops\Tables\MediaTables;
use App\Models\Shop;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class ShopMediasTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public int $shopId;

    public function table(Table $table): Table
    {
        $shop = $this->getShop();
        $formSchema = fn (): array => MediaForm::configure(new Schema($this), $shop)->getComponents();

        return MediaTables::configure(
            $table->query(fn () => $shop->medias()->getQuery()),
        )
            ->headerActions([
                CreateAction::make()
                    ->schema($formSchema),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema($formSchema),
                DeleteAction::make(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.merchant.table');
    }

    private function getShop(): Shop
    {
        return Shop::findOrFail($this->shopId);
    }
}
