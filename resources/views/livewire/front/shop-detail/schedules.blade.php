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
                                @elseif ($schedule->isByAppointment() && $schedule->morning_start === null && $schedule->noon_start === null)
                                    <span class="text-amber-600">Sur rendez-vous</span>
                                @elseif ($schedule->morning_end === null && $schedule->noon_start === null)
                                    {{ $schedule->morning_start ? substr((string) $schedule->morning_start, 0, 5) : '' }}
                                    {{ $schedule->noon_end ? '- ' . substr((string) $schedule->noon_end, 0, 5) : '' }}
                                    @if ($schedule->isByAppointment())
                                        <span class="ml-1 text-amber-600">(sur rendez-vous)</span>
                                    @endif
                                @else
                                    @if ($schedule->morning_start)
                                        {{ substr((string) $schedule->morning_start, 0, 5) }}
                                        {{ $schedule->morning_end ? '- ' . substr((string) $schedule->morning_end, 0, 5) : '' }}
                                        @if ($schedule->is_morning_by_appointment)
                                            <span class="ml-1 text-amber-600">(sur rendez-vous)</span>
                                        @endif
                                    @endif
                                    @if ($schedule->noon_start)
                                        @if ($schedule->morning_start)
                                            <span class="mx-1 text-slate-400">|</span>
                                        @endif
                                        {{ substr((string) $schedule->noon_start, 0, 5) }}
                                        {{ $schedule->noon_end ? '- ' . substr((string) $schedule->noon_end, 0, 5) : '' }}
                                        @if ($schedule->is_noon_by_appointment)
                                            <span class="ml-1 text-amber-600">(sur rendez-vous)</span>
                                        @endif
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
