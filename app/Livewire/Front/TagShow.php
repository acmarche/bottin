<?php

declare(strict_types=1);

namespace App\Livewire\Front;

use App\Models\Shop;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.front')]
final class TagShow extends Component
{
    public Tag $tag;

    public function mount(Tag $tag): void
    {
        $this->tag = $tag;
    }

    public function render(): View
    {
        $shops = Shop::query()
            ->whereHas('tags', fn ($q) => $q->where('tags.id', $this->tag->id))
            ->with('categories', 'media')
            ->orderBy('company')
            ->get();

        return view('livewire.front.tag-show', [
            'shops' => $shops,
        ])->title($this->tag->name);
    }
}
