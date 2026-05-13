@php
    $hasContactAddress = $shop->contact_street || $shop->contact_number || $shop->contact_postal_code || $shop->contact_city;
    $hasContactDetails = $shop->contact_phone || $shop->contact_phone_other || $shop->contact_mobile || $shop->contact_email;
@endphp
@if ($hasContactAddress || $hasContactDetails)
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-semibold text-slate-900">Personne de contact</h2>
        <div class="mt-3 space-y-2 text-base text-slate-600">
            @if ($hasContactAddress)
                <p>{{ trim($shop->contact_street . ' ' . $shop->contact_number) }}</p>
                <p>{{ trim($shop->contact_postal_code . ' ' . $shop->contact_city) }}</p>
            @endif

            @if ($shop->contact_phone)
                <p class="flex items-center gap-2 {{ $hasContactAddress ? 'pt-2' : '' }}">
                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                    <a href="tel:{{ $shop->contact_phone }}" class="hover:text-stormy-teal-600 transition">{{ $shop->contact_phone }}</a>
                </p>
            @endif

            @if ($shop->contact_phone_other)
                <p class="flex items-center gap-2">
                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                    <a href="tel:{{ $shop->contact_phone_other }}" class="hover:text-stormy-teal-600 transition">{{ $shop->contact_phone_other }}</a>
                    <span class="text-xs text-slate-400">(autre)</span>
                </p>
            @endif

            @if ($shop->contact_mobile)
                <p class="flex items-center gap-2">
                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                    <a href="tel:{{ $shop->contact_mobile }}" class="hover:text-stormy-teal-600 transition">{{ $shop->contact_mobile }}</a>
                </p>
            @endif

            @if ($shop->contact_email)
                <p class="flex items-center gap-2">
                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                    <a href="mailto:{{ $shop->contact_email }}" class="hover:text-stormy-teal-600 transition">{{ $shop->contact_email }}</a>
                </p>
            @endif
        </div>
    </div>
@endif
