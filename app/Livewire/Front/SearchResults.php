<?php

declare(strict_types=1);

namespace App\Livewire\Front;

use App\Models\Category;
use App\Models\Shop;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.front')]
#[Title('Recherche')]
final class SearchResults extends Component
{
    #[Url(as: 'q')]
    public string $search = '';

    public function render(): View
    {
        $shops = new Collection();
        $categories = new Collection();

        if (mb_strlen($this->search) >= 2) {
            $shops = Shop::search($this->search)
                ->query(fn ($query) => $query
                    ->with('categories', 'media')
                    ->orderBy('company')
                )
                ->take(50)
                ->get();

            $categories = Category::search($this->search)
                ->query(fn ($query) => $query->orderBy('name'))
                ->take(20)
                ->get();
        }

        return view('livewire.front.search-results', [
            'shops' => $shops,
            'categories' => $categories,
        ]);
    }
}
