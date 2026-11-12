<article class="service-card">
    <span class="eyebrow">{{ $service->category->name }}</span>
    <h3><a href="{{ $service->publicUrl() }}">{{ $service->title }}</a></h3>
    @if ($service->short_description)<p>{{ $service->short_description }}</p>@endif
    <a class="text-link" href="{{ $service->publicUrl() }}">Explore service <span aria-hidden="true">→</span></a>
</article>
