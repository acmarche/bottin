<?php

declare(strict_types=1);

namespace App\Livewire\Front;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.front')]
#[Title('Accueil')]
final class HomePage extends Component
{
    public function render(): View
    {
        /** @var Collection<int, Category> $rootCategories */
        $rootCategories = Category::query()
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        return view('livewire.front.home-page', [
            'rootCategories' => $rootCategories,
        ]);
    }
}
