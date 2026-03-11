<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

final class BelgianAddressService
{
    private const string BASE_URL = 'https://best.pr.fedservices.be/api/opendata/best/v1/belgianAddress/v2';

    /**
     * @return list<string>
     */
    public static function streetsByPostalCode(string $postalCode, string $search = ''): array
    {
        if ($postalCode === '') {
            return [];
        }

        $streets = Cache::remember(
            "belgian_streets_{$postalCode}",
            now()->addWeek(),
            fn (): array => self::fetchStreets($postalCode),
        );

        if ($search !== '') {
            $search = mb_strtolower($search);
            $streets = array_values(array_filter(
                $streets,
                fn (string $street): bool => str_contains(mb_strtolower($street), $search),
            ));
        }

        return array_slice($streets, 0, 20);
    }

    /**
     * @return list<string>
     */
    private static function fetchStreets(string $postalCode): array
    {
        try {
            $response = Http::withHeaders([
                'BelGov-Trace-Id' => uuid_create(),
            ])->get(self::BASE_URL.'/streets', [
                'postCode' => $postalCode,
            ]);

            if (! $response->successful()) {
                return [];
            }

            $items = $response->json('items', []);

            return collect($items)
                ->map(fn (array $item): ?string => $item['name']['fr'] ?? $item['name']['nl'] ?? $item['name']['de'] ?? null)
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all();
        } catch (Throwable) {
            return [];
        }
    }
}
