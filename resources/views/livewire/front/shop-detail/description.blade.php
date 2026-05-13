@if ($shop->comment1 || $shop->comment2 || $shop->comment3)
    <div class="prose prose-slate max-w-none rounded-xl border border-slate-200 bg-white p-6">
        @if ($shop->comment1)
            <div>{!! nl2br(e($shop->comment1)) !!}</div>
        @endif
        @if ($shop->comment2)
            <div class="mt-4">{!! nl2br(e($shop->comment2)) !!}</div>
        @endif
        @if ($shop->comment3)
            <div class="mt-4">{!! nl2br(e($shop->comment3)) !!}</div>
        @endif
    </div>
@endif
