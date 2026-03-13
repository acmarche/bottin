<x-layouts.front>
    <x-slot:title>Erreur serveur</x-slot:title>

    <section class="flex min-h-[70vh] items-center justify-center px-4 py-16">
        <div class="mx-auto max-w-lg text-center">
            {{-- Animated robot with sparks --}}
            <div class="relative mx-auto mb-8 size-48">
                {{-- Smoke puffs --}}
                <svg class="absolute left-6 top-0 size-10 animate-ping text-slate-300 opacity-40" viewBox="0 0 40 40" fill="currentColor">
                    <circle cx="20" cy="20" r="12" />
                </svg>
                <svg class="absolute right-8 top-2 size-8 animate-ping text-slate-200 opacity-30" style="animation-delay: 0.5s" viewBox="0 0 40 40" fill="currentColor">
                    <circle cx="20" cy="20" r="14" />
                </svg>
                <svg class="absolute left-16 -top-2 size-6 animate-ping text-slate-300 opacity-25" style="animation-delay: 1s" viewBox="0 0 40 40" fill="currentColor">
                    <circle cx="20" cy="20" r="10" />
                </svg>

                {{-- Robot body --}}
                <svg class="absolute inset-0 size-full animate-[wiggle_0.3s_ease-in-out_infinite]" viewBox="0 0 200 200" fill="none">
                    {{-- Antenna --}}
                    <line x1="100" y1="35" x2="100" y2="55" stroke="#94a3b8" stroke-width="3" stroke-linecap="round" />
                    <circle cx="100" cy="30" r="6" class="animate-pulse" fill="#ef4444" />

                    {{-- Head --}}
                    <rect x="60" y="55" width="80" height="55" rx="12" fill="#475569" />

                    {{-- Eyes - X marks (broken!) --}}
                    <g stroke="#ef4444" stroke-width="3" stroke-linecap="round">
                        <line x1="80" y1="72" x2="90" y2="82" />
                        <line x1="90" y1="72" x2="80" y2="82" />
                        <line x1="110" y1="72" x2="120" y2="82" />
                        <line x1="120" y1="72" x2="110" y2="82" />
                    </g>

                    {{-- Mouth - zigzag (glitching) --}}
                    <polyline points="75,98 82,93 89,98 96,93 103,98 110,93 117,98 124,93" stroke="#fbbf24" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" />

                    {{-- Neck --}}
                    <rect x="90" y="110" width="20" height="10" rx="2" fill="#64748b" />

                    {{-- Body --}}
                    <rect x="50" y="120" width="100" height="50" rx="10" fill="#475569" />

                    {{-- Chest panel with warning --}}
                    <rect x="75" y="130" width="50" height="30" rx="5" fill="#334155" />
                    <polygon points="100,134 106,146 94,146" fill="#fbbf24" />
                    <line x1="100" y1="138" x2="100" y2="143" stroke="#334155" stroke-width="2" stroke-linecap="round" />
                    <circle cx="100" cy="145.5" r="1" fill="#334155" />

                    {{-- Arms --}}
                    <rect x="25" y="125" width="22" height="40" rx="8" fill="#64748b" />
                    <rect x="153" y="125" width="22" height="40" rx="8" fill="#64748b" />

                    {{-- Bolts on arms --}}
                    <circle cx="36" cy="140" r="3" fill="#94a3b8" />
                    <circle cx="164" cy="140" r="3" fill="#94a3b8" />

                    {{-- Sparks flying --}}
                    <g class="animate-pulse">
                        <polygon points="38,118 42,112 46,118 43,118 47,108 40,115" fill="#fbbf24" />
                        <polygon points="155,115 159,109 163,115 160,115 164,105 157,112" fill="#fbbf24" />
                    </g>

                    {{-- Legs --}}
                    <rect x="65" y="170" width="18" height="20" rx="5" fill="#64748b" />
                    <rect x="117" y="170" width="18" height="20" rx="5" fill="#64748b" />

                    {{-- Feet --}}
                    <rect x="60" y="187" width="28" height="8" rx="4" fill="#475569" />
                    <rect x="112" y="187" width="28" height="8" rx="4" fill="#475569" />
                </svg>
            </div>

            <style>
                @keyframes wiggle {
                    0%, 100% { transform: rotate(-1.5deg); }
                    50% { transform: rotate(1.5deg); }
                }
            </style>

            <h1 class="text-7xl font-black tracking-tight text-stormy-teal-800">500</h1>

            <p class="mt-4 text-2xl font-semibold text-slate-700">Notre robot a disjoncté !</p>
            <p class="mt-2 text-slate-500">
                Le serveur a rencontré un petit souci technique.
                Pas de panique, nos techniciens sont déjà en train de le réparer avec du café et du courage.
            </p>

            <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                <a
                    href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-stormy-teal-800 px-6 py-3 font-semibold text-white shadow-lg transition hover:bg-stormy-teal-700 hover:shadow-xl"
                >
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Retour à l'accueil
                </a>
                <a
                    href="javascript:location.reload()"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 font-semibold text-slate-700 transition hover:border-pearl-aqua-400 hover:text-stormy-teal-700"
                >
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182" />
                    </svg>
                    Réessayer
                </a>
            </div>
        </div>
    </section>
</x-layouts.front>
