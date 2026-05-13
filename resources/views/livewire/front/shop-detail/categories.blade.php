@if ($shop->categories->isNotEmpty())
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-semibold text-slate-900">Cat&eacute;gories</h2>
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach ($shop->categories as $category)
                <a href="{{ route('category.show', $category) }}" class="rounded-full bg-alice-blue-50 px-3 py-1 text-xs font-medium text-alice-blue-700 transition hover:bg-alice-blue-100">{{ $category->name }}</a>
            @endforeach
        </div>
    </div>
@endif
