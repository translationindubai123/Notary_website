{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($staticPages as $page)
        <url><loc>{{ $page['url'] }}</loc></url>
    @endforeach
    @foreach ($categories as $category)
        @if (!$category->canonical_url)
            <url><loc>{{ $category->publicCategoryUrl() }}</loc><lastmod>{{ $category->updated_at->toAtomString() }}</lastmod></url>
        @endif
    @endforeach
    @foreach ($services as $service)
        @if (!$service->canonical_url)
            <url><loc>{{ $service->publicUrl() }}</loc><lastmod>{{ $service->updated_at->toAtomString() }}</lastmod></url>
        @endif
    @endforeach
</urlset>
