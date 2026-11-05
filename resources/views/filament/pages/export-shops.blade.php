<x-filament-panels::page>
    <x-filament::section
        heading="Catégories"
        description="Cochez une catégorie pour inclure toutes ses sous-catégories, ou dépliez-la pour n'en choisir que certaines."
    >
        <ul class="space-y-1">
            @foreach ($tree as $node)
                @include('filament.pages.export-shops.category-node', ['node' => $node])
            @endforeach
        </ul>
    </x-filament::section>

    <div class="sticky bottom-4 z-10 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <p class="text-sm text-gray-700 dark:text-gray-200">
            {{ trans_choice(':count catégorie sélectionnée|:count catégories sélectionnées', $selectedCategoriesCount) }}
            —
            <span class="font-semibold">{{ trans_choice(':count fiche|:count fiches', $selectedShopsCount) }}</span>
        </p>

        <div class="flex items-center gap-3">
            @if ($selectedCategoriesCount > 0)
                <x-filament::link tag="button" color="gray" wire:click="clearSelection">
                    Tout désélectionner
                </x-filament::link>
            @endif

            {{ $this->exportAction }}
        </div>
    </div>
</x-filament-panels::page>
