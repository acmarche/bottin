<div class="space-y-4">
    @foreach ($groups as $group)
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-2">
                {{ $group['company'] }} — {{ $group['city'] }}
            </h3>
            <ul class="space-y-1">
                @foreach ($group['shops'] as $shop)
                    <li>
                        <a
                            href="{{ $shop['url'] }}"
                            class="text-base text-primary-600 hover:underline dark:text-primary-400"
                        >
                            {{ $shop['company'] }} — {{ $shop['city'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>
