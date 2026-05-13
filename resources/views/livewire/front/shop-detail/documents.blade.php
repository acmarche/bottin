@if ($shop->getMedia('documents')->isNotEmpty())
    <div>
        <h2 class="text-lg font-semibold text-slate-900">Documents</h2>
        <ul class="mt-3 space-y-2">
            @foreach ($shop->getMedia('documents') as $document)
                <li>
                    <a href="{{ $document->getUrl() }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-base text-slate-700 transition hover:bg-slate-50">
                        <svg class="size-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                        {{ $document->name ?: $document->file_name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
