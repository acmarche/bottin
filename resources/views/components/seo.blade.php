@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'image' => null,
    'type' => 'website',
    'noindex' => false,
])

@php
    $seoUrl = $canonical ?? url()->current();
    $seoTitle = $title ?? config('app.name');
    $seoDescription = filled($description)
        ? \Illuminate\Support\Str::limit(trim((string) preg_replace('/\s+/', ' ', strip_tags((string) $description))), 160)
        : null;
@endphp

@push('meta')
    <meta name="robots" content="{{ $noindex ? 'noindex, follow' : 'index, follow' }}">
    @if ($seoDescription)
        <meta name="description" content="{{ $seoDescription }}">
    @endif
    <link rel="canonical" href="{{ $seoUrl }}">

    <meta property="og:type" content="{{ $type }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    @if ($seoDescription)
        <meta property="og:description" content="{{ $seoDescription }}">
    @endif
    @if ($image)
        <meta property="og:image" content="{{ $image }}">
    @endif

    <meta name="twitter:card" content="{{ $image ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    @if ($seoDescription)
        <meta name="twitter:description" content="{{ $seoDescription }}">
    @endif
    @if ($image)
        <meta name="twitter:image" content="{{ $image }}">
    @endif
@endpush
