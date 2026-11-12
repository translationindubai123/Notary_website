@extends('layouts.app')
@section('title', ($category->meta_title ?: $category->name).' | '.$siteSettings['site_name'])
@section('meta_description', $category->meta_description ?: $category->short_description ?: 'Service information for '.$category->name.'.')
@section('canonical', $category->canonical_url ?: $category->publicCategoryUrl())
@section('content')
    <section class="page-hero"><div class="container narrow"><span class="eyebrow">{{ $serviceType === 'moj' ? 'MOJ service category' : 'Service category' }}</span><h1>{{ $category->name }}</h1>@if ($category->short_description)<p>{{ $category->short_description }}</p>@endif</div></section>
    @if ($category->description)<section class="section section-tight category-description"><div class="container narrow content-prose">{!! nl2br(e($category->description)) !!}</div></section>@endif
    @if ($children->isNotEmpty())<section class="section section-tight"><div class="container"><h2 class="mb-4">Explore subcategories</h2><div class="category-grid category-grid-subcategories">@foreach ($children as $index => $child)<x-category-card :category="$child" :index="$index" />@endforeach</div></div></section>@endif
    <section class="section"><div class="container">
        @if ($services->isNotEmpty())<div class="service-grid">@foreach ($services as $service)<x-service-card :service="$service" />@endforeach</div>@else<p class="empty-state">There are no published services in this category yet.</p>@endif
        <div class="pagination-wrap">{{ $services->links() }}</div>
    </div></section>
    <x-cta />
@endsection
