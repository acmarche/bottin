<?php

declare(strict_types=1);

namespace App\Livewire\Front;

use App\Models\Shop;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.front')]
final class ShopDetail extends Component
{
    public Shop $shop;

    public function mount(Shop $shop): void
    {
        $this->shop = $shop->load([
            'categories',
            'schedules' => fn ($q) => $q->orderBy('day'),
            'media',
            'tags.tagGroup',
        ]);
    }

    /** @return array<int, string> */
    public function getDayNames(): array
    {
        return [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        ];
    }

    public function render(): View
    {
        return view('livewire.front.shop-detail', [
            'dayNames' => $this->getDayNames(),
        ])->title($this->shop->company);
    }
}
