<?php

declare(strict_types=1);

namespace App\Livewire\Front;

use App\Models\Shop;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

use function mb_strtoupper;
use function mb_substr;

#[Layout('components.layouts.front')]
#[Title('Annuaire A-Z')]
final class ShopIndex extends Component
{
    #[Url]
    public string $letter = 'A';

    public function selectLetter(string $letter): void
    {
        $this->letter = mb_strtoupper($letter);
    }

    public function render(): View
    {
        $shops = Shop::query()
            ->where('company', 'like', $this->letter.'%')
            ->orderBy('company')
            ->get();

        /** @var Collection<string, Collection<int, Shop>> $grouped */
        $grouped = $shops->groupBy(fn (Shop $shop): string => mb_strtoupper(mb_substr((string) $shop->company, 0, 1)));

        $alphabet = range('A', 'Z');

        return view('livewire.front.shop-index', [
            'grouped' => $grouped,
            'alphabet' => $alphabet,
        ]);
    }
}
