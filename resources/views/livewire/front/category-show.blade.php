<div>
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="mb-6 text-sm text-slate-500" aria-label="Fil d'Ariane">
            <a href="{{ route('home') }}" class="hover:text-stormy-teal-600 transition">Accueil</a>
            @if ($category->parent)
                <span class="mx-1">/</span>
                <a href="{{ route('category.show', $category->parent) }}" class="hover:text-stormy-teal-600 transition">{{ $category->parent->name }}</a>
            @endif
            <span class="mx-1">/</span>
            <span class="text-slate-800 font-medium">{{ $category->name }}</span>
        </nav>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">{{ $category->name }}</h1>
            @if ($category->description)
                <p class="mt-2 text-slate-600">{{ $category->description }}</p>
            @endif
        </div>

        {{-- Sub-categories --}}
        @if ($category->children->isNotEmpty())
            <div class="mb-8 flex flex-wrap gap-2">
                @foreach ($category->children as $child)
                    <a
                        href="{{ route('category.show', $child) }}"
                        class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-1.5 text-sm font-medium text-slate-700 shadow-sm transition hover:border-pearl-aqua-400 hover:text-pearl-aqua-700 hover:shadow"
                    >
                        {{ $child->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Shops --}}
        @if ($shops->isNotEmpty())
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($shops as $shop)
                    <a href="{{ route('shop.show', $shop) }}" class="group flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md hover:border-pearl-aqua-300" wire:key="shop-{{ $shop->id }}">
                        @php
                            $mainImage = $shop->medias->firstWhere('is_main', true) ?? $shop->medias->first();
                        @endphp
                        @if ($mainImage)
                            <div class="h-40 overflow-hidden rounded-t-xl bg-slate-100">
                                <img src="{{ asset('storage/' . $mainImage->storagePath()) }}" alt="{{ $shop->company }}" class="size-full object-cover transition group-hover:scale-105">
                            </div>
                        @endif
                        <div class="flex flex-1 flex-col p-4">
                            <h3 class="font-semibold text-slate-900 group-hover:text-stormy-teal-700 transition">{{ $shop->company }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $shop->city }}{{ $shop->phone ? ' — ' . $shop->phone : '' }}</p>
                            @if ($shop->categories->isNotEmpty())
                                <div class="mt-auto flex flex-wrap gap-1 pt-3">
                                    @foreach ($shop->categories->take(3) as $cat)
                                        <span class="rounded-full bg-alice-blue-50 px-2 py-0.5 text-xs font-medium text-alice-blue-700">{{ $cat->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="py-12 text-center text-slate-500">Aucun commerce dans cette cat&eacute;gorie.</p>
        @endif
    </section>
</div>
