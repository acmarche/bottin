<div>
    {{-- Hero --}}
    <section class="bg-linear-to-br from-stormy-teal-800 to-stormy-teal-950 px-4 py-16 text-white sm:py-24">
        <div class="mx-auto max-w-3xl text-center">
            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">{{ config('app.name') }}</h1>
            <p class="mt-4 text-lg text-stormy-teal-200">Trouvez les commerces et services pr&egrave;s de chez vous</p>
            <form action="{{ route('search') }}" method="get" class="mx-auto mt-8 max-w-xl">
                <div class="relative">
                    <input
                        type="search"
                        name="q"
                        placeholder="Rechercher un commerce, une cat&eacute;gorie, une ville..."
                        class="w-full rounded-xl border-0 bg-white/95 px-5 py-4 pr-12 text-slate-800 shadow-lg placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-white/30"
                    >
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg bg-stormy-teal-700 p-2 text-white transition hover:bg-stormy-teal-800">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- Categories --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-slate-900">Cat&eacute;gories</h2>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
            @foreach ($rootCategories as $category)
                <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md hover:border-pearl-aqua-300">
                    <a href="{{ route('category.show', $category) }}" class="block">
                        <div class="flex items-center gap-3">
                            @if ($category->icon)
                                <span class="text-2xl">{{ $category->icon }}</span>
                            @else
                                <span class="flex size-10 items-center text-xl justify-center rounded-lg font-bold text-white" style="background-color: {{ $category->color ?: '#008c99' }}">
                                    {{ mb_strtoupper(mb_substr($category->name, 0, 1)) }}
                                </span>
                            @endif
                            <h3 class="font-semibold text-xl text-slate-900 group-hover:text-stormy-teal-700 transition">{{ $category->name }}</h3>
                        </div>
                    </a>
                    @if ($category->children->isNotEmpty())
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach ($category->children->take(6) as $child)
                                <a
                                    href="{{ route('category.show', $child) }}"
                                    class="inline-block rounded-full bg-slate-100 px-2.5 py-0.5 font-medium text-slate-600 transition hover:bg-pearl-aqua-100 hover:text-pearl-aqua-700"
                                >
                                    {{ $child->name }}
                                </a>
                            @endforeach
                            @if ($category->children->count() > 6)
                                <span class="inline-block rounded-full bg-slate-100 px-2.5 py-0.5 text-slate-500">+{{ $category->children->count() - 6 }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
</div>
