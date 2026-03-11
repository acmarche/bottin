@php use App\Filament\Resources\Shops\ShopResource; @endphp
<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @if (mb_strlen($this->company) >= 2)
            @if ($this->results->isNotEmpty())
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ trans_choice('{1} :count existing shop found|[2,*] :count existing shops found', $this->results->count(), ['count' => $this->results->count()]) }}
                    </h3>
                    <ul class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($this->results as $shop)
                            <li class="py-2">
                                <a href="{{ ShopResource::getUrl('view', ['record' => $shop]) }}"
                                   class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-400">
                                    {{ $shop->company }}
                                </a>
                                @if ($shop->city)
                                    <span
                                        class="text-sm text-gray-500 dark:text-gray-400"> &mdash; {{ $shop->city }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('No existing fiche found for this name.') }}
                    </p>
                </div>
            @endif

            <x-filament::button wire:click="create" icon="heroicon-o-plus">
                {{ 'Créer la fiche' }}
            </x-filament::button>
        @endif
    </div>
</x-filament-panels::page>
