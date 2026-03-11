<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Shops\ShopResource;
use App\Models\Category;
use App\Models\Shop;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

final class DataQualityOverview extends StatsOverviewWidget implements HasActions
{
    use InteractsWithActions;

    protected ?string $pollingInterval = null;

    protected string $view = 'filament.widgets.data-quality-overview';

    public function showDuplicatesAction(): Action
    {
        return Action::make('showDuplicates')
            ->label('Doublons')
            ->modalHeading('Doublons (entreprises)')
            ->modalDescription('Fiches avec le même nom et code postal')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Fermer')
            ->modalContent(fn (): View => view('filament.widgets.duplicate-shops-modal', [
                'groups' => $this->getDuplicateGroups(),
            ]));
    }

    protected function getHeading(): ?string
    {
        return 'Checkup';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Fiches sans catégorie', $this->shopsWithoutCategories())
                ->description('Fiches non classées')
                ->color('danger')
                ->url(ShopResource::getUrl('index', ['tableFilters[without_category][value]' => '1'])),

            Stat::make('Catégories sans fiche', $this->categoriesWithoutShops())
                ->description('Catégories vides')
                ->color('warning')
                ->url(CategoryResource::getUrl('index', ['tableFilters[without_shop][value]' => '1'])),

            Stat::make('Doublons (entreprises)', $this->duplicateShops())
                ->description('Même nom et code postal')
                ->color('danger')
                ->extraAttributes([
                    'wire:click' => 'mountAction(\'showDuplicates\')',
                    'class' => 'cursor-pointer',
                ]),
        ];
    }

    /** @return array<int, array{company: string, postal_code: string, shops: array<int, array{company: string, city: string, url: string}>}> */
    private function getDuplicateGroups(): array
    {
        $duplicatePairs = DB::table('shops')
            ->select('company', 'postal_code')
            ->groupBy('company', 'postal_code')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $groups = [];

        foreach ($duplicatePairs as $pair) {
            $shops = Shop::query()
                ->where('company', $pair->company)
                ->where('postal_code', $pair->postal_code)
                ->orderBy('city')
                ->get();

            $groups[] = [
                'company' => $pair->company,
                'postal_code' => $pair->postal_code ?? '',
                'shops' => $shops->map(fn (Shop $shop): array => [
                    'company' => $shop->company,
                    'city' => $shop->city ?? '',
                    'url' => ShopResource::getUrl('view', ['record' => $shop->id]),
                ])->all(),
            ];
        }

        return $groups;
    }

    private function shopsWithoutCategories(): int
    {
        return Shop::query()
            ->whereDoesntHave('categories')
            ->count();
    }

    private function categoriesWithoutShops(): int
    {
        return Category::query()
            ->leaves()
            ->whereDoesntHave('shops')
            ->count();
    }

    private function duplicateShops(): int
    {
        return (int) DB::table('shops')
            ->select('company', 'postal_code')
            ->groupBy('company', 'postal_code')
            ->havingRaw('COUNT(*) > 1')
            ->count();
    }
}
