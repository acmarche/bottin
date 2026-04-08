<div>
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="mb-6 text-base text-slate-500" aria-label="Fil d'Ariane">
            <a href="{{ route('home') }}" class="hover:text-stormy-teal-600 transition">Accueil</a>
            <span class="mx-1">/</span>
            <span class="text-slate-800 font-medium">{{ $tag->name }}</span>
        </nav>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">{{ $tag->name }}</h1>
            @if ($tag->description)
                <p class="mt-2 text-slate-600">{{ $tag->description }}</p>
            @endif
        </div>

        {{-- Shops --}}
        @if ($shops->isNotEmpty())
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($shops as $shop)
                    <a href="{{ route('shop.show', $shop) }}" class="group flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md hover:border-pearl-aqua-300" wire:key="shop-{{ $shop->id }}">
                        @php
                            $mainImage = $shop->getFirstMedia('images', fn($m) => $m->getCustomProperty('is_main')) ?? $shop->getFirstMedia('images');
                        @endphp
                        @if ($mainImage)
                            <div class="aspect-video overflow-hidden rounded-t-xl bg-slate-100">
                                <img
                                    src="{{ $mainImage->getUrl() }}"
                                    alt="{{ $shop->company }}"
                                    loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                    decoding="async"
                                    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
                                    class="size-full object-cover transition group-hover:scale-105"
                                >
                            </div>
                        @endif
                        <div class="flex flex-1 flex-col p-4">
                            <h3 class="font-semibold text-slate-900 group-hover:text-stormy-teal-700 transition">{{ $shop->company }}</h3>
                            <p class="mt-1 text-base text-slate-500">{{ $shop->city }}{{ $shop->phone ? ' — ' . $shop->phone : '' }}</p>
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
            <p class="py-12 text-center text-slate-500">Aucun commerce avec ce label.</p>
        @endif
    </section>
</div>
