{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('shops.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
@foreach ($categories as $category)
    <url>
        <loc>{{ route('category.show', $category) }}</loc>
@if ($category->updated_at)
        <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
@endif
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
@endforeach
@foreach ($tags as $tag)
    <url>
        <loc>{{ route('tag.show', $tag) }}</loc>
@if ($tag->updated_at)
        <lastmod>{{ $tag->updated_at->toAtomString() }}</lastmod>
@endif
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
@endforeach
@foreach ($shops as $shop)
    <url>
        <loc>{{ route('shop.show', $shop) }}</loc>
@if ($shop->updated_at)
        <lastmod>{{ $shop->updated_at->toAtomString() }}</lastmod>
@endif
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
@endforeach
</urlset>
