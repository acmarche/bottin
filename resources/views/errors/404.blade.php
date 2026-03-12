<x-layouts.front>
    <x-slot:title>Page introuvable</x-slot:title>

    <section class="flex min-h-[70vh] items-center justify-center px-4 py-16">
        <div class="mx-auto max-w-lg text-center">
            {{-- Animated magnifying glass searching --}}
            <div class="relative mx-auto mb-8 size-48">
                {{-- Map/city background --}}
                <svg class="absolute inset-0 size-full text-slate-200" viewBox="0 0 200 200" fill="none">
                    {{-- Buildings --}}
                    <rect x="20" y="100" width="25" height="60" rx="2" fill="currentColor" />
                    <rect x="50" y="80" width="20" height="80" rx="2" fill="currentColor" />
                    <rect x="75" y="110" width="30" height="50" rx="2" fill="currentColor" />
                    <rect x="110" y="90" width="22" height="70" rx="2" fill="currentColor" />
                    <rect x="140" y="105" width="28" height="55" rx="2" fill="currentColor" />
                    {{-- Road --}}
                    <rect x="0" y="160" width="200" height="8" rx="4" class="text-slate-300" fill="currentColor" />
                    {{-- Little trees --}}
                    <circle cx="45" cy="148" r="8" class="text-pearl-aqua-200" fill="currentColor" />
                    <circle cx="105" cy="150" r="6" class="text-pearl-aqua-200" fill="currentColor" />
                    <circle cx="170" cy="147" r="7" class="text-pearl-aqua-200" fill="currentColor" />
                </svg>
                {{-- Magnifying glass --}}
                <svg class="absolute left-1/2 top-1/2 size-24 -translate-x-1/2 -translate-y-1/2 animate-bounce text-stormy-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    {{-- Question mark inside lens --}}
                    <text x="10.5" y="13" text-anchor="middle" font-size="8" font-weight="bold" fill="currentColor" stroke="none">?</text>
                </svg>
            </div>

            <h1 class="text-7xl font-black tracking-tight text-stormy-teal-800">404</h1>

            <p class="mt-4 text-2xl font-semibold text-slate-700">Oups, cette page s'est perdue !</p>
            <p class="mt-2 text-slate-500">
                On a cherch&eacute; partout dans l'annuaire, mais cette page reste introuvable.
                Peut-&ecirc;tre qu'elle est partie ouvrir son propre commerce ?
            </p>

            <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-stormy-teal-800 px-6 py-3 font-semibold text-white shadow-lg transition hover:bg-stormy-teal-700 hover:shadow-xl"
                >
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Retour &agrave; l'accueil
                </a>
                <a
                    href="{{ route('shops.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 font-semibold text-slate-700 transition hover:border-pearl-aqua-400 hover:text-stormy-teal-700"
                >
                    Parcourir l'annuaire
                </a>
            </div>
        </div>
    </section>
</x-layouts.front>
