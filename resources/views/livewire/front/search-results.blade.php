<div>
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">R&eacute;sultats de recherche</h1>
        @if ($search)
            <p class="mt-2 text-slate-500">R&eacute;sultats pour &laquo;&nbsp;{{ $search }}&nbsp;&raquo;</p>
        @endif

        {{-- Category results --}}
        @if ($categories->isNotEmpty())
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-slate-900">Cat&eacute;gories</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($categories as $category)
                        <a
                            href="{{ route('category.show', $category) }}"
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-1.5 text-base font-medium text-slate-700 shadow-sm transition hover:border-pearl-aqua-400 hover:text-pearl-aqua-700"
                        >
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Shop results --}}
        @if ($shops->isNotEmpty())
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-slate-900">Commerces ({{ $shops->count() }})</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
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
            </div>
        @endif

        @if ($search && $shops->isEmpty() && $categories->isEmpty())
            <p class="mt-12 text-center text-slate-500">Aucun r&eacute;sultat trouv&eacute; pour &laquo;&nbsp;{{ $search }}&nbsp;&raquo;</p>
        @endif

        @if (!$search)
            <p class="mt-12 text-center text-slate-500">Entrez un terme de recherche pour commencer.</p>
        @endif
    </section>
</div>
