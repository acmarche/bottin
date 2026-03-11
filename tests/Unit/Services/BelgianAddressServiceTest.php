<?php

declare(strict_types=1);

use App\Services\BelgianAddressService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('returns empty array for empty postal code', function (): void {
    expect(BelgianAddressService::streetsByPostalCode(''))->toBe([]);
});

it('fetches streets from the BeST API and returns sorted names', function (): void {
    Http::fake([
        'best.pr.fedservices.be/*' => Http::response([
            'items' => [
                ['name' => ['fr' => 'Rue de la Gare']],
                ['name' => ['fr' => 'Avenue de France']],
                ['name' => ['fr' => 'Chemin du Bois']],
            ],
        ]),
    ]);

    Cache::flush();

    $streets = BelgianAddressService::streetsByPostalCode('6900');

    expect($streets)->toBe([
        'Avenue de France',
        'Chemin du Bois',
        'Rue de la Gare',
    ]);

    Http::assertSentCount(1);
});

it('falls back to nl or de when fr name is missing', function (): void {
    Http::fake([
        'best.pr.fedservices.be/*' => Http::response([
            'items' => [
                ['name' => ['nl' => 'Kerkstraat']],
                ['name' => ['de' => 'Kirchstraße']],
                ['name' => ['fr' => 'Rue Haute']],
            ],
        ]),
    ]);

    Cache::flush();

    $streets = BelgianAddressService::streetsByPostalCode('1000');

    expect($streets)->toBe([
        'Kerkstraat',
        'Kirchstraße',
        'Rue Haute',
    ]);
});

it('caches results per postal code', function (): void {
    Http::fake([
        'best.pr.fedservices.be/*' => Http::response([
            'items' => [
                ['name' => ['fr' => 'Rue Neuve']],
            ],
        ]),
    ]);

    Cache::flush();

    BelgianAddressService::streetsByPostalCode('5000');
    BelgianAddressService::streetsByPostalCode('5000');

    Http::assertSentCount(1);
});

it('filters streets by search term', function (): void {
    Http::fake([
        'best.pr.fedservices.be/*' => Http::response([
            'items' => [
                ['name' => ['fr' => 'Rue de la Gare']],
                ['name' => ['fr' => 'Avenue de France']],
                ['name' => ['fr' => 'Rue du Marché']],
                ['name' => ['fr' => 'Chemin du Bois']],
            ],
        ]),
    ]);

    Cache::flush();

    $streets = BelgianAddressService::streetsByPostalCode('6900', 'rue');

    expect($streets)->toBe([
        'Rue de la Gare',
        'Rue du Marché',
    ]);
});

it('returns empty array on API failure', function (): void {
    Http::fake([
        'best.pr.fedservices.be/*' => Http::response([], 500),
    ]);

    Cache::flush();

    expect(BelgianAddressService::streetsByPostalCode('9999'))->toBe([]);
});
