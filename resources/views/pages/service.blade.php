@extends('layouts.app')
@section('title', ($service->meta_title ?: $service->title).' | '.$siteSettings['site_name'])
@section('meta_description', $service->meta_description ?: $service->short_description ?: 'Information about '.$service->title.'.')
@section('og_title', $service->og_title ?: $service->meta_title ?: $service->title)
@section('og_description', $service->og_description ?: $service->meta_description ?: $service->short_description ?: 'Information about '.$service->title.'.')
@section('robots', ($isPreview ?? false) ? 'noindex,nofollow' : 'index,follow')
@section('canonical', ($isPreview ?? false) ? url()->current() : ($service->canonical_url ?: $service->publicUrl()))
@push('head')
    <script type="application/ld+json">@json($structuredData)</script>
    @if ($faqData)<script type="application/ld+json">@json($faqData)</script>@endif
@endpush
@section('content')
    @if ($isPreview ?? false)
        <div class="container">
            <div class="alert alert-warning mt-3" role="status">
                @if ($isPublished)
                    Admin preview — this service is publicly published.
                    <a href="{{ $service->publicUrl() }}">View the public page</a>.
                @else
                    Admin preview — this content is not publicly published.
                @endif
            </div>
        </div>
    @endif

    <section class="page-hero service-detail-hero">
        <div class="container service-detail-hero-inner {{ $service->featured_image ? 'has-image' : '' }}">
            <div class="service-detail-heading">
                <a class="service-category-link" href="{{ $service->category->publicCategoryUrl() }}"><span aria-hidden="true">←</span> {{ $service->category->name }}</a>
                <h1>{{ $service->title }}</h1>
                @if ($service->introduction)
                    <p>{{ $service->introduction }}</p>
                @elseif ($service->short_description)
                    <p>{{ $service->short_description }}</p>
                @endif
                <div class="hero-actions">
                    <a class="button button-dark" href="{{ route('contact', ['service' => $service->slug]) }}">Ask about this service <span aria-hidden="true">→</span></a>
                    @if ($siteSettings['site_phone'])
                        <a class="button button-outline" data-contact-method="phone" href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings['site_phone']) }}">Call {{ $siteSettings['site_phone'] }}</a>
                    @endif
                </div>
            </div>
            @if ($service->featured_image)
                <div class="service-detail-image"><img src="{{ asset('storage/'.$service->featured_image) }}" alt="{{ $service->image_alt ?: $service->title }}"></div>
            @endif
        </div>
    </section>

    <section class="section service-detail-content">
        <div class="container article-layout service-detail-layout">
            <article class="content-prose service-content">
                @if ($service->description)
                    <section class="service-content-card" id="about-service"><span class="eyebrow">Overview</span><h2>About this service</h2>{!! nl2br(e($service->description)) !!}</section>
                @endif
                @if ($service->eligibility)
                    <section class="service-content-card" id="eligibility"><span class="eyebrow">Before you proceed</span><h2>Eligibility and requirements</h2>{!! nl2br(e($service->eligibility)) !!}</section>
                @endif
                @if ($service->who_needs_service)
                    <section class="service-content-card" id="who-needs-service"><span class="eyebrow">Who this is for</span><h2>Who may need this service</h2>{!! nl2br(e($service->who_needs_service)) !!}</section>
                @endif
                @if ($service->documents->isNotEmpty())
                    <section class="service-content-card" id="required-documents"><span class="eyebrow">Prepare your documents</span><h2>Required documents</h2><ul class="service-document-list">@foreach ($service->documents as $document)<li><strong>{{ $document->document_name }}</strong>@if ($document->document_description)<span>{{ $document->document_description }}</span>@endif</li>@endforeach</ul></section>
                @endif
                @if ($service->processSteps->isNotEmpty())
                    <section class="service-content-card" id="process-steps"><span class="eyebrow">What to expect</span><h2>Process</h2><ol class="service-process-list">@foreach ($service->processSteps as $step)<li><span class="service-step-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $step->step_title }}</h3><p>{{ $step->step_description }}</p></div></li>@endforeach</ol></section>
                @endif
                @if ($service->processing_time || $service->service_availability)
                    <section class="service-content-card" id="service-details"><span class="eyebrow">At a glance</span><h2>Service details</h2><dl class="service-facts">@if ($service->processing_time)<div><dt>Processing time</dt><dd>{{ $service->processing_time }}</dd></div>@endif @if ($service->service_availability)<div><dt>Availability</dt><dd>{{ $service->service_availability }}</dd></div>@endif</dl></section>
                @endif
                @if ($service->important_notes)
                    <section class="service-content-card" id="important-notes"><span class="eyebrow">Please note</span><h2>Important notes</h2>{!! nl2br(e($service->important_notes)) !!}</section>
                @endif
                @if ($service->faqs->isNotEmpty())
                    <section class="service-content-card" id="service-faqs"><span class="eyebrow">Common questions</span><h2>Frequently asked questions</h2><div class="faq-list service-faq-list">@foreach ($service->faqs as $faq)<details><summary>{{ $faq->question }}</summary><p>{{ $faq->answer }}</p></details>@endforeach</div></section>
                @endif
            </article>

            <aside class="service-sidebar">
                @if ($service->description || $service->eligibility || $service->who_needs_service || $service->documents->isNotEmpty() || $service->processSteps->isNotEmpty() || $service->processing_time || $service->service_availability || $service->important_notes || $service->faqs->isNotEmpty())
                    <nav class="service-contents" aria-label="On this page">
                        <h2>On this page</h2>
                        <ul>
                            @if ($service->description)<li><a href="#about-service">About this service</a></li>@endif
                            @if ($service->eligibility)<li><a href="#eligibility">Eligibility and requirements</a></li>@endif
                            @if ($service->who_needs_service)<li><a href="#who-needs-service">Who may need it</a></li>@endif
                            @if ($service->documents->isNotEmpty())<li><a href="#required-documents">Required documents</a></li>@endif
                            @if ($service->processSteps->isNotEmpty())<li><a href="#process-steps">Process</a></li>@endif
                            @if ($service->processing_time || $service->service_availability)<li><a href="#service-details">Service details</a></li>@endif
                            @if ($service->important_notes)<li><a href="#important-notes">Important notes</a></li>@endif
                            @if ($service->faqs->isNotEmpty())<li><a href="#service-faqs">FAQs</a></li>@endif
                        </ul>
                    </nav>
                @endif
                <div class="service-inquiry-card">
                    <span class="eyebrow">Need help?</span>
                    <h2>Talk to our team</h2>
                    <p>Send us an inquiry about this service and we’ll get back to you.</p>
                    <a class="button button-dark w-100" href="{{ route('contact', ['service' => $service->slug]) }}">Make an inquiry <span aria-hidden="true">→</span></a>
                    @if ($siteSettings['site_whatsapp'])
                        <a class="sidebar-contact-link" data-contact-method="whatsapp" target="_blank" rel="noopener noreferrer" aria-label="Contact us on WhatsApp" href="https://wa.me/{{ preg_replace('/\D/', '', $siteSettings['site_whatsapp']) }}">Ask on WhatsApp</a>
                    @endif
                    @if ($service->processing_time)
                        <p class="sidebar-detail"><strong>Processing time</strong><br>{{ $service->processing_time }}</p>
                    @endif
                </div>
            </aside>
        </div>
    </section>

    @if ($service->relatedServices->isNotEmpty())
        <section class="section section-soft service-related-section">
            <div class="container">
                <div class="section-heading"><div><span class="eyebrow">Explore further</span><h2>Related services</h2></div></div>
                <div class="service-grid">@foreach ($service->relatedServices as $related)<x-service-card :service="$related" />@endforeach</div>
            </div>
        </section>
    @endif
    @include('components.cta')
@endsection
