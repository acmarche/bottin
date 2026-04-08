<?php

declare(strict_types=1);

namespace App\Livewire\Front;

use App\Models\Category;
use App\Models\Shop;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.front')]
final class CategoryShow extends Component
{
    public Category $category;

    public function mount(Category $category): void
    {
        $this->category = $category->load('children', 'parent');
    }

    public function render(): View
    {
        $categoryIds = $this->getDescendantIds($this->category);
        $categoryIds->push($this->category->id);

        $shops = Shop::query()
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->with('categories', 'media')
            ->orderBy('company')
            ->get();

        return view('livewire.front.category-show', [
            'shops' => $shops,
        ])->title($this->category->name);
    }

    /** @return Collection<int, int> */
    private function getDescendantIds(Category $category): Collection
    {
        $ids = new Collection();

        foreach ($category->children as $child) {
            $ids->push($child->id);
            $ids = $ids->merge($this->getDescendantIds($child));
        }

        return $ids;
    }
}
