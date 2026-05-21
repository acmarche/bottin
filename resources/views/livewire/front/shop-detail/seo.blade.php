@php
    $seoImage = $shop->getFirstMediaUrl('images', 'detail') ?: null;
    $seoDescription = $shop->comment1
        ?: trim("{$shop->company} — {$shop->street} {$shop->number}, {$shop->postal_code} {$shop->city}", " —,");

    $jsonLd = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => $shop->company,
        'url' => url()->current(),
        'telephone' => $shop->phone ?: $shop->mobile,
        'email' => $shop->email,
        'image' => $seoImage,
        'address' => array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => trim("{$shop->street} {$shop->number}"),
            'postalCode' => $shop->postal_code,
            'addressLocality' => $shop->city,
            'addressCountry' => 'BE',
        ]),
        'geo' => ($shop->latitude && $shop->longitude) ? [
            '@type' => 'GeoCoordinates',
            'latitude' => (float) $shop->latitude,
            'longitude' => (float) $shop->longitude,
        ] : null,
        'vatID' => $shop->vat_number,
    ]);
@endphp

<x-seo
    :title="$shop->company"
    :description="$seoDescription"
    :image="$seoImage"
    type="business.business"
/>

@push('meta')
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
