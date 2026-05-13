@if ($shop->getMedia('images')->isNotEmpty())
    <div>
        <div class="mt-3 grid gap-3 grid-cols-2 sm:grid-cols-3">
            @foreach ($shop->getMedia('images') as $image)
                <div class="overflow-hidden rounded-lg bg-slate-100 aspect-4/3">
                    {{ $image->img('detail', [
                        'alt' => $shop->company . ($image->name ? ' — ' . $image->name : ''),
                        'loading' => $loop->first ? 'eager' : 'lazy',
                        'decoding' => 'async',
                        'sizes' => '(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw',
                        'class' => 'size-full object-contain',
                    ]) }}
                </div>
            @endforeach
        </div>
    </div>
@endif
