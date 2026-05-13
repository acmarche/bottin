@php
    $publicTags = $shop->tags->where('private', false);
@endphp
@if ($publicTags->isNotEmpty())
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-semibold text-slate-900">Labels</h2>
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach ($publicTags as $tag)
                <a href="{{ route('tag.show', $tag) }}" class="rounded-full px-3 py-1 text-xs font-medium transition hover:opacity-80" style="background-color: {{ ($tag->color ?: '#e2e8f0') . '20' }}; color: {{ $tag->color ?: '#475569' }}">
                    {{ $tag->name }}
                </a>
            @endforeach
        </div>
    </div>
@endif
