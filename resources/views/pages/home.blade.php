@extends('layouts.app')

@section('title', 'Notary Services in Dubai | '.$siteSettings['site_name'])
@section('meta_description', $siteSettings['home_meta_description'] ?: 'Looking for notary services in Dubai? Explore document notarisation, powers of attorney, company documents, legal notices and other notary services. Contact our team for guidance.')
@section('canonical', route('home'))
@push('head')
    @if ($homepageFaqData)<script type="application/ld+json">@json($homepageFaqData)</script>@endif
@endpush
@section('content')
    <section class="hero home-hero">
        <div class="container hero-grid">
            <div class="hero-copy">
                <span class="eyebrow">{{ $siteSettings['home_hero_eyebrow'] }}</span>
                <h1>{{ $siteSettings['home_hero_title'] }}</h1>
                <p>{{ $siteSettings['home_hero_introduction'] }}</p>
                <div class="hero-actions">
                    <a class="button button-gold" href="{{ route('services.index') }}">{{ $siteSettings['home_primary_cta'] }}</a>
                    <a class="button button-outline" href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings['site_phone']) }}">{{ $siteSettings['home_secondary_cta'] }}</a>
                </div>
                <p class="hero-disclaimer">Service requirements depend on the document and the relevant authority.</p>
            </div>
            <x-contact-form :services="$services" :selected-service-id="$selectedServiceId" compact />
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <section class="section section-soft home-categories">
            <div class="container" data-scroll-reveal>
                <div class="section-heading section-heading-intro">
                    <div><span class="eyebrow">Our services</span><h2>Notary Services for Different Document Requirements</h2><p>Every document has a purpose, and the appropriate process depends on what needs to be signed, certified, amended or submitted. Explore our service categories to find information relevant to your requirements.</p></div>
                    <a class="text-link" href="{{ route('services.index') }}">View all services <span aria-hidden="true">→</span></a>
                </div>
                <div class="category-grid">@foreach ($categories as $index => $category)<x-category-card :category="$category" :index="$index" />@endforeach</div>
            </div>
        </section>
    @endif

    @if ($featuredServices->isNotEmpty())
        <section class="section home-featured">
            <div class="container" data-scroll-reveal>
                <div class="section-heading section-heading-intro">
                    <div><span class="eyebrow">Selected services</span><h2>Explore Our Notary Services in Dubai</h2><p>Whether you are preparing a personal document or handling a business transaction, start by selecting the service that best matches your requirements.</p></div>
                    <a class="text-link" href="{{ route('services.index') }}">Browse all services <span aria-hidden="true">→</span></a>
                </div>
                <div class="service-grid">@foreach ($featuredServices as $service)<x-service-card :service="$service" />@endforeach</div>
            </div>
        </section>
    @endif

    <section class="section section-soft home-benefits">
        <div class="container" data-scroll-reveal>
            <div class="section-heading section-heading-intro">
                <div><span class="eyebrow">A clear service experience</span><h2>Clear Information. A More Straightforward Service Experience.</h2><p>Choosing a document service can be confusing when requirements vary. Our aim is to help visitors understand the available service information and identify an appropriate next step.</p></div>
            </div>
            <div class="benefit-grid">
                <article class="benefit-card"><span aria-hidden="true">01</span><h3>Organised Service Information</h3><p>Find service descriptions, relevant requirements and next steps in one place.</p></article>
                <article class="benefit-card"><span aria-hidden="true">02</span><h3>Document-Specific Guidance</h3><p>Understand which information may be needed for your chosen service.</p></article>
                <article class="benefit-card"><span aria-hidden="true">03</span><h3>Convenient Enquiries</h3><p>Contact the team to clarify your requirements before proceeding.</p></article>
                <article class="benefit-card"><span aria-hidden="true">04</span><h3>Attention to Requirements</h3><p>Review the relevant service details and confirm current requirements before submission.</p></article>
            </div>
        </div>
    </section>

    <section class="process-band home-process">
        <div class="container process-layout" data-scroll-reveal>
            <div><span class="eyebrow eyebrow-light">How to get started</span><h2>How to Get Started with Notary Services in Dubai</h2><p class="process-intro">The process depends on the document and service selected. This is a general inquiry journey, not a promise that every document follows an identical legal procedure.</p></div>
            <ol class="process-list">
                <li><span>01</span><div><h3>Choose Your Service</h3><p>Browse the service categories and select the document or procedure relevant to your needs.</p></div></li>
                <li><span>02</span><div><h3>Review the Requirements</h3><p>Read the available information and confirm requirements and procedure with the relevant service provider or authority.</p></div></li>
                <li><span>03</span><div><h3>Submit an Enquiry</h3><p>Share the type of service you need and your preferred contact method so the team can respond.</p></div></li>
                <li><span>04</span><div><h3>Confirm the Next Steps</h3><p>Proceed after applicable requirements, fees, documents and process have been confirmed.</p></div></li>
            </ol>
        </div>
    </section>

    <section class="section home-document-groups">
        <div class="container" data-scroll-reveal>
            <div class="section-heading section-heading-intro">
                <div><span class="eyebrow">Find relevant information</span><h2>Notary Services for Individuals and Businesses</h2><p>Different circumstances call for different documents. Explore the available service information that may be relevant to your needs.</p></div>
            </div>
            <div class="document-groups">
                <article><span class="document-group-mark" aria-hidden="true">P</span><div><h3>Personal Documents</h3><p>Explore applicable services for powers of attorney, declarations, acknowledgements and other personal legal documents.</p><a class="text-link" href="{{ route('services.index', ['q' => 'power of attorney']) }}">Explore related services <span aria-hidden="true">→</span></a></div></article>
                <article><span class="document-group-mark" aria-hidden="true">B</span><div><h3>Corporate Documents</h3><p>Find information about company documents, corporate instruments, agreements and related services.</p><a class="text-link" href="{{ route('services.index', ['q' => 'company']) }}">Explore related services <span aria-hidden="true">→</span></a></div></article>
                <article><span class="document-group-mark" aria-hidden="true">N</span><div><h3>Agreements and Notices</h3><p>Explore available agreement, notice and document-correction services, subject to the scope of the service offered.</p><a class="text-link" href="{{ route('services.index', ['q' => 'notice']) }}">Explore related services <span aria-hidden="true">→</span></a></div></article>
            </div>
        </div>
    </section>

    <section class="section section-soft home-international-documents">
        <div class="container">
            <div class="section-heading section-heading-intro">
                <div><span class="eyebrow">International document information</span><h2>Documents Connected to Different Countries</h2><p>Explore service information that may be relevant to documents connected with these countries. Requirements depend on the document, its intended use and the relevant authority.</p></div>
            </div>
            <div class="home-country-support">
                <ul class="home-country-flags">
                    <li><img src="{{ asset('images/flags/canada.svg') }}" alt="Flag of Canada" width="90" height="45" loading="lazy"><span>Canada</span></li>
                    <li><img src="{{ asset('images/flags/pakistan.svg') }}" alt="Flag of Pakistan" width="90" height="60" loading="lazy"><span>Pakistan</span></li>
                    <li><img src="{{ asset('images/flags/united-kingdom.svg') }}" alt="Flag of the United Kingdom" width="90" height="45" loading="lazy"><span>United Kingdom</span></li>
                    <li><img src="{{ asset('images/flags/united-states.svg') }}" alt="Flag of the United States" width="95" height="50" loading="lazy"><span>United States</span></li>
                    <li><img src="{{ asset('images/flags/china.svg') }}" alt="Flag of China" width="90" height="60" loading="lazy"><span>China</span></li>
                    <li><img src="{{ asset('images/flags/philippines.svg') }}" alt="Flag of the Philippines" width="90" height="45" loading="lazy"><span>Philippines</span></li>
                </ul>
            </div>
        </div>
    </section>

    <section class="section section-soft home-distinctions">
        <div class="container distinction-layout">
            <div><span class="eyebrow">Know the difference</span><h2>Notarisation, Attestation and Legal Translation</h2><p>These are different services, although one document may require more than one. The appropriate route depends on where a document was issued, where it will be used, its language and the receiving authority’s requirements.</p><a class="button button-outline" href="{{ route('services.index') }}">Explore Our Services</a></div>
            <dl class="distinction-list">
                <div><dt>Notarisation</dt><dd>A notarial act for an eligible document, signature or declaration, depending on applicable requirements.</dd></div>
                <div><dt>Attestation</dt><dd>A process used to verify a document or an official signature or seal for its intended use.</dd></div>
                <div><dt>Legal translation</dt><dd>Translation in the format and by the provider required for the relevant purpose.</dd></div>
            </dl>
        </div>
    </section>

    <section class="moj-home home-moj">
        <div class="container moj-home-inner">
            <div><span class="eyebrow eyebrow-light">Related information</span><h2>Explore Ministry of Justice-Related Services</h2><p>Find information about the MOJ-related services available through our service catalogue. Review relevant service details to understand applicable requirements and next steps.</p><p class="moj-disclaimer">Service information does not imply government affiliation or endorsement.</p></div>
            <a class="button button-light" href="{{ route('moj') }}">Explore MOJ Services <span aria-hidden="true">→</span></a>
        </div>
    </section>

    <section class="section home-faq">
        <div class="container faq-home-layout">
            <div class="section-heading section-heading-intro"><div><span class="eyebrow">Service information</span><h2>Frequently Asked Questions About Notary Services in Dubai</h2><p>Find answers to common questions about notary services, documents, fees and next steps in Dubai. Requirements depend on the document and relevant authority.</p></div></div>
            <div class="faq-list">
                @foreach ($homepageFaqs as $faq)
                <details><summary>{{ $faq['question'] }}</summary><div class="faq-answer"><p>{{ $faq['answer'] }}</p></div></details>
                @endforeach
            </div>
        </div>
    </section>

    <section class="cta-band home-cta">
        <div class="container cta-inner"><div><span class="eyebrow eyebrow-light">Need help finding the right service?</span><h2>Let’s Discuss Your Document Requirements</h2><p>Explore our notary services or contact the team to discuss your document, understand possible next steps and confirm applicable requirements.</p></div><div class="cta-actions"><a class="button button-light" href="{{ route('contact') }}">Send an Enquiry <span aria-hidden="true">→</span></a>@if ($siteSettings['site_whatsapp'])<a class="cta-whatsapp" data-contact-method="whatsapp" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp the team" href="https://wa.me/{{ preg_replace('/\D/', '', $siteSettings['site_whatsapp']) }}">WhatsApp the Team</a>@endif</div></div>
    </section>
@endsection
