<div>
    <h1 class="text-3xl font-bold text-slate-900">{{ $shop->company }}</h1>

    @if ($shop->vat_number)
        <p class="mt-1 text-sm text-slate-500">TVA&nbsp;: {{ $shop->vat_number }}</p>
    @endif

    @php
        $badgeTags = $shop->tags->where('private', false);
    @endphp
    <div class="mt-3 flex flex-wrap gap-2">
        @if ($badgeTags->contains('name', 'Pmr'))
            <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">PMR</span>
        @endif
        @if ($badgeTags->contains('name', 'Click & Collect'))
            <span class="inline-flex items-center gap-1 rounded-full bg-alice-blue-50 px-3 py-1 text-xs font-semibold text-alice-blue-700">Click &amp; Collect</span>
        @endif
        @if ($badgeTags->contains('name', 'Ecommerce'))
            <span class="inline-flex items-center gap-1 rounded-full bg-pearl-aqua-50 px-3 py-1 text-xs font-semibold text-pearl-aqua-700">E-commerce</span>
        @endif
    </div>
</div>
