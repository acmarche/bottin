<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur-sm">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/cropped-ADL_Logo-seul_Noir.png') }}" alt="ADL" class="h-9">
                    <span class="text-xl font-bold tracking-tight text-stormy-teal-800">{{ config('app.name') }}</span>
                </a>

                {{-- Desktop nav --}}
                <div class="hidden items-center gap-6 md:flex">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 transition hover:text-stormy-teal-600">Accueil</a>
                    <a href="{{ route('shops.index') }}" class="text-sm font-medium text-slate-600 transition hover:text-stormy-teal-600">Annuaire A-Z</a>
                    <a href="https://circuit-court.marche.be" class="text-sm font-medium text-slate-600 transition hover:text-stormy-teal-600">Circuit court</a>
                    <a href="{{ route('filament.admin.auth.login') }}" class="text-sm font-medium text-slate-600 transition hover:text-stormy-teal-600">Connexion</a>
                    <form action="{{ route('search') }}" method="get" class="relative">
                        <input
                            type="search"
                            name="q"
                            placeholder="Rechercher..."
                            value="{{ request('q') }}"
                            class="w-56 rounded-full border border-slate-300 bg-slate-50 py-1.5 pl-9 pr-3 text-sm transition placeholder:text-slate-400 focus:border-pearl-aqua-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-pearl-aqua-200"
                        >
                        <svg class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </form>
                </div>

                {{-- Mobile hamburger --}}
                <button
                    x-data
                    x-on:click="$dispatch('toggle-mobile-nav')"
                    class="inline-flex items-center justify-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 md:hidden"
                    aria-label="Menu"
                >
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </nav>

            {{-- Mobile menu --}}
            <div
                x-data="{ open: false }"
                x-on:toggle-mobile-nav.window="open = !open"
                x-show="open"
                x-collapse
                class="border-t border-slate-200 md:hidden"
            >
                <div class="space-y-1 px-4 py-3">
                    <a href="{{ route('home') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Accueil</a>
                    <a href="{{ route('shops.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Annuaire A-Z</a>
                    <a href="https://circuit-court.marche.be" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Circuit court</a>
                    <a href="{{ route('filament.admin.auth.login') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Connexion</a>
                    <form action="{{ route('search') }}" method="get" class="pt-2">
                        <input
                            type="search"
                            name="q"
                            placeholder="Rechercher..."
                            value="{{ request('q') }}"
                            class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm placeholder:text-slate-400 focus:border-pearl-aqua-400 focus:outline-none focus:ring-2 focus:ring-pearl-aqua-200"
                        >
                    </form>
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="mt-16 border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
                    <div class="flex items-center gap-6">
                        <img src="{{ asset('images/cropped-ADL_Logo-seul_Noir.png') }}" alt="ADL" class="h-10">
                        <img src="{{ asset('images/Marche_logo.png') }}" alt="Ville de Marche-en-Famenne" class="h-10">
                    </div>
                    <p class="text-sm text-slate-500">&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits r&eacute;serv&eacute;s.</p>
                </div>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
