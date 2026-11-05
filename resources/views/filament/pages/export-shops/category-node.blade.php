<li wire:key="category-{{ $node['id'] }}" x-data="{ open: false }">
    <div class="flex items-center gap-2 rounded-lg px-1 py-1 hover:bg-gray-50 dark:hover:bg-white/5">
        @if ($node['children'] !== [])
            <button
                type="button"
                class="flex size-6 shrink-0 items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                x-on:click="open = ! open"
                x-bind:aria-expanded="open"
                aria-label="Déplier {{ $node['name'] }}"
            >
                <x-filament::icon
                    icon="heroicon-m-chevron-right"
                    class="size-4 transition"
                    x-bind:class="{ 'rotate-90': open }"
                />
            </button>
        @else
            <span class="size-6 shrink-0"></span>
        @endif

        <label class="flex min-w-0 cursor-pointer items-center gap-2 text-sm text-gray-950 dark:text-white">
            {{-- The key follows the state: a replaced input is the only reliable way to refresh checked/indeterminate after a morph. --}}
            <input
                wire:key="category-checkbox-{{ $node['id'] }}-{{ $node['state'] }}"
                type="checkbox"
                class="fi-checkbox-input"
                wire:click="toggleCategory({{ $node['id'] }})"
                wire:loading.attr="disabled"
                @checked($node['state'] === 'checked')
                @if ($node['state'] === 'partial') x-init="$el.indeterminate = true" @endif
            />
            <span class="truncate">{{ $node['name'] }}</span>
        </label>
    </div>

    @if ($node['children'] !== [])
        <ul class="ms-6 space-y-1" x-show="open" x-cloak>
            @foreach ($node['children'] as $child)
                @include('filament.pages.export-shops.category-node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
