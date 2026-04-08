<div>
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Back button --}}
        <div class="mb-6">
            <a href="javascript:history.back()" class="inline-flex items-center gap-1 text-base font-medium text-slate-500 transition hover:text-stormy-teal-600">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
                Retour
            </a>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            {{-- Main content --}}
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">{{ $shop->company }}</h1>

                    {{-- Badges --}}
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

                {{-- Description --}}
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

                {{-- Images gallery --}}
                @if ($shop->getMedia('images')->isNotEmpty())
                    <div>
                        <div class="mt-3 grid gap-3 grid-cols-2 sm:grid-cols-3">
                            @foreach ($shop->getMedia('images') as $image)
                                <div class="overflow-hidden rounded-lg bg-slate-100 aspect-[4/3]">
                                    <img
                                        src="{{ $image->getUrl() }}"
                                        alt="{{ $shop->company }}{{ $image->name ? ' — ' . $image->name : '' }}"
                                        loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                        decoding="async"
                                        sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                                        class="size-full object-cover"
                                    >
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Schedules --}}
                @if ($shop->schedules->isNotEmpty())
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Horaires</h2>
                        <div class="mt-3 overflow-hidden rounded-xl border border-slate-200 bg-white">
                            <table class="w-full text-base">
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($shop->schedules as $schedule)
                                        <tr>
                                            <td class="px-4 py-2.5 font-medium text-slate-700">{{ $dayNames[$schedule->day] ?? '' }}</td>
                                            <td class="px-4 py-2.5 text-slate-600">
                                                @if ($schedule->is_closed)
                                                    <span class="text-red-500">Ferm&eacute;</span>
                                                @elseif ($schedule->is_by_appointment)
                                                    <span class="text-amber-600">Sur rendez-vous</span>
                                                @elseif ($schedule->is_open_at_lunch)
                                                    {{ $schedule->morning_start ? substr((string) $schedule->morning_start, 0, 5) : '' }}
                                                    {{ $schedule->noon_end ? '- ' . substr((string) $schedule->noon_end, 0, 5) : '' }}
                                                @else
                                                    {{ $schedule->morning_start ? substr((string) $schedule->morning_start, 0, 5) : '' }}
                                                    {{ $schedule->morning_end ? '- ' . substr((string) $schedule->morning_end, 0, 5) : '' }}
                                                    @if ($schedule->noon_start)
                                                        <span class="mx-1 text-slate-400">|</span>
                                                        {{ substr((string) $schedule->noon_start, 0, 5) }}
                                                        {{ $schedule->noon_end ? '- ' . substr((string) $schedule->noon_end, 0, 5) : '' }}
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Admin panel link --}}
                @auth
                    <a
                        href="{{ route('filament.admin.resources.shops.edit', $shop) }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-base font-medium text-slate-700 transition hover:bg-slate-50 w-full justify-center"
                    >
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-15a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v15a2.25 2.25 0 0 0 2.25 2.25z" /></svg>
                        Éditer dans l'admin
                    </a>
                @endauth

                {{-- Contact --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="font-semibold text-slate-900">Contact</h2>
                    <div class="mt-3 space-y-2 text-base text-slate-600">
                        <p>{{ $shop->street }} {{ $shop->number }}</p>
                        <p>{{ $shop->postal_code }} {{ $shop->city }}</p>

                        @if ($shop->phone)
                            <p class="flex items-center gap-2 pt-2">
                                <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                                <a href="tel:{{ $shop->phone }}" class="hover:text-stormy-teal-600 transition">{{ $shop->phone }}</a>
                            </p>
                        @endif

                        @if ($shop->mobile)
                            <p class="flex items-center gap-2">
                                <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                                <a href="tel:{{ $shop->mobile }}" class="hover:text-stormy-teal-600 transition">{{ $shop->mobile }}</a>
                            </p>
                        @endif

                        @if ($shop->email)
                            <p class="flex items-center gap-2">
                                <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                                <a href="mailto:{{ $shop->email }}" class="hover:text-stormy-teal-600 transition">{{ $shop->email }}</a>
                            </p>
                        @endif

                        @if ($shop->website)
                            <p class="flex items-center gap-2">
                                <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5a17.92 17.92 0 0 1-8.716-2.247m0 0A9 9 0 0 1 3 12c0-1.47.353-2.856.978-4.082" /></svg>
                                <a href="{{ $shop->website }}" target="_blank" rel="noopener" class="hover:text-stormy-teal-600 transition truncate">{{ parse_url($shop->website, PHP_URL_HOST) ?: $shop->website }}</a>
                            </p>
                        @endif
                    </div>

                    {{-- Social --}}
                    @if ($shop->facebook || $shop->instagram || $shop->twitter || $shop->linkedin || $shop->youtube || $shop->tiktok)
                        <div class="mt-4 flex gap-3 border-t border-slate-100 pt-4">
                            @if ($shop->facebook)
                                <a href="{{ $shop->facebook }}" target="_blank" rel="noopener" class="text-slate-400 transition hover:text-blue-600" aria-label="Facebook">
                                    <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                            @endif
                            @if ($shop->instagram)
                                <a href="{{ $shop->instagram }}" target="_blank" rel="noopener" class="text-slate-400 transition hover:text-pink-500" aria-label="Instagram">
                                    <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                            @endif
                            @if ($shop->linkedin)
                                <a href="{{ $shop->linkedin }}" target="_blank" rel="noopener" class="text-slate-400 transition hover:text-blue-700" aria-label="LinkedIn">
                                    <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                </a>
                            @endif
                            @if ($shop->youtube)
                                <a href="{{ $shop->youtube }}" target="_blank" rel="noopener" class="text-slate-400 transition hover:text-red-600" aria-label="YouTube">
                                    <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            @endif
                            @if ($shop->tiktok)
                                <a href="{{ $shop->tiktok }}" target="_blank" rel="noopener" class="text-slate-400 transition hover:text-slate-900" aria-label="TikTok">
                                    <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Categories --}}
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

                {{-- Tags --}}
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

                {{-- Map link --}}
                @if ($shop->latitude && $shop->longitude)
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="font-semibold text-slate-900">Localisation</h2>
                        <a
                            href="https://www.openstreetmap.org/?mlat={{ $shop->latitude }}&mlon={{ $shop->longitude }}#map=17/{{ $shop->latitude }}/{{ $shop->longitude }}"
                            target="_blank"
                            rel="noopener"
                            class="mt-3 inline-flex items-center gap-2 rounded-lg bg-stormy-teal-50 px-4 py-2 text-base font-medium text-stormy-teal-800 transition hover:bg-stormy-teal-100"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            Voir sur la carte
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
