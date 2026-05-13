@if ($shop->latitude && $shop->longitude)
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-semibold text-slate-900">Localisation</h2>
        <a
            href="https://www.openstreetmap.org/?mlat={{ $shop->latitude }}&mlon={{ $shop->longitude }}#map=17/{{ $shop->latitude }}/{{ $shop->longitude }}"
            target="_blank"
            rel="noopener"
            class="mt-3 inline-flex items-center gap-2 rounded-lg bg-stormy-teal-50 px-4 py-2 text-base font-medium text-stormy-teal-800 transition hover:bg-stormy-teal-100"
        >
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
            Voir sur la carte
        </a>
    </div>
@endif
