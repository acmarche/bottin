<div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="font-semibold text-slate-900">Contact</h2>
    <div class="mt-3 space-y-2 text-base text-slate-600">
        @if ($shop->first_name || $shop->last_name || $shop->civility)
            <p class="font-medium text-slate-700">{{ trim(($shop->civility ? $shop->civility . ' ' : '') . $shop->first_name . ' ' . $shop->last_name) }}</p>
            @if ($shop->function)
                <p class="text-sm text-slate-500">{{ $shop->function }}</p>
            @endif
        @endif
        <p>{{ $shop->street }} {{ $shop->number }}</p>
        <p>{{ $shop->postal_code }} {{ $shop->city }}</p>

        @if ($shop->phone)
            <p class="flex items-center gap-2 pt-2">
                <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                <a href="tel:{{ $shop->phone }}" class="hover:text-stormy-teal-600 transition">{{ $shop->phone }}</a>
            </p>
        @endif

        @if ($shop->phone_other)
            <p class="flex items-center gap-2">
                <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                <a href="tel:{{ $shop->phone_other }}" class="hover:text-stormy-teal-600 transition">{{ $shop->phone_other }}</a>
                <span class="text-xs text-slate-400">(autre)</span>
            </p>
        @endif

        @if ($shop->mobile)
            <p class="flex items-center gap-2">
                <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                <a href="tel:{{ $shop->mobile }}" class="hover:text-stormy-teal-600 transition">{{ $shop->mobile }}</a>
            </p>
        @endif

        @if ($shop->email)
            <p class="flex items-center gap-2">
                <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                <a href="mailto:{{ $shop->email }}" class="hover:text-stormy-teal-600 transition">{{ $shop->email }}</a>
            </p>
        @endif

        @if ($shop->website)
            <p class="flex items-center gap-2">
                <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5a17.92 17.92 0 0 1-8.716-2.247m0 0A9 9 0 0 1 3 12c0-1.47.353-2.856.978-4.082" /></svg>
                <a href="{{ $shop->website }}" target="_blank" rel="noopener" class="hover:text-stormy-teal-600 transition truncate">{{ parse_url($shop->website, PHP_URL_HOST) ?: $shop->website }}</a>
            </p>
        @endif
    </div>

    @include('livewire.front.shop-detail.social')
</div>
