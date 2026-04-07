<div>
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">Annuaire A-Z</h1>

        {{-- Alphabet bar --}}
        <nav class="sticky top-[57px] z-30 -mx-4 mt-6 flex flex-wrap gap-1 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur-sm sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" aria-label="Lettres de l'alphabet">
            @foreach ($alphabet as $l)
                <button
                    wire:click="selectLetter('{{ $l }}')"
                    @class([
                        'flex size-9 items-center justify-center rounded-lg text-base font-medium transition',
                        'bg-stormy-teal-700 text-white shadow-sm' => $letter === $l,
                        'text-slate-600 hover:bg-slate-100 hover:text-stormy-teal-700' => $letter !== $l,
                    ])
                >
                    {{ $l }}
                </button>
            @endforeach
        </nav>

        {{-- Shop list --}}
        <div class="mt-6 space-y-2">
            @forelse ($grouped as $initial => $shops)
                <h2 class="sticky top-[120px] z-20 -mx-4 bg-slate-50 px-4 py-2 text-lg font-bold text-stormy-teal-700 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">{{ $initial }}</h2>
                <div class="divide-y divide-slate-100">
                    @foreach ($shops as $shop)
                        <a href="{{ route('shop.show', $shop) }}" class="flex items-center justify-between gap-4 rounded-lg px-3 py-3 transition hover:bg-white hover:shadow-sm" wire:key="shop-{{ $shop->id }}">
                            <div class="min-w-0">
                                <p class="font-medium text-slate-900 truncate">{{ $shop->company }}</p>
                                <p class="text-base text-slate-500 truncate">{{ $shop->city }}{{ $shop->phone ? ' — ' . $shop->phone : '' }}</p>
                            </div>
                            <svg class="size-5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            @empty
                <p class="py-12 text-center text-slate-500">Aucun commerce trouv&eacute; pour la lettre &laquo;&nbsp;{{ $letter }}&nbsp;&raquo;</p>
            @endforelse
        </div>
    </section>
</div>
