@extends('layouts.app')
@section('title', 'About Us | '.$siteSettings['site_name'].' Dubai')
@section('meta_description', 'Learn about Al Syed Documents Clearing Services in Dubai. Discover our commitment to professional document clearing, notary assistance, and reliable documentation support.')
@section('canonical', rtrim(route('about'), '/').'/')
@php($breadcrumbs = [['label' => 'Home', 'url' => route('home')], ['label' => 'About Us']])
@section('content')
    <section class="about-hero">
        <div class="container about-hero-layout">
            <div class="about-hero-copy">
                <span class="eyebrow">About Us</span>
                <h1>Making Every Document Journey Simpler</h1>
                <p class="about-hero-lead">At <strong>{{ $siteSettings['site_name'] }}</strong>, we believe dealing with legal paperwork, official documentation, and government procedures should not be complicated or stressful.</p>
                <p>In a city as dynamic as Dubai, individuals, families, entrepreneurs, and businesses often need reliable assistance navigating documentation requirements and administrative processes. Our purpose is to make these journeys easier through professional guidance, attentive service, and dependable document-clearing support.</p>
                <p>Located in the heart of Dubai, we are committed to helping our clients move forward with greater clarity, confidence, and peace of mind.</p>
            </div>
            <aside class="about-hero-card">
                <span class="about-card-mark" aria-hidden="true">AS</span>
                <span class="eyebrow">Your Dubai support partner</span>
                <h2>Clear guidance.<br>Personal support.</h2>
                <p>Helping you understand documentation requirements and take the next step with confidence.</p>
                <a class="text-link" href="{{ route('contact') }}">Talk to our team <span aria-hidden="true">→</span></a>
            </aside>
        </div>
    </section>

    <section class="section about-story">
        <div class="container">
            <div class="about-section-heading">
                <span class="eyebrow">Our story</span>
                <h2>Built Around a Simpler Way</h2>
                <p>Behind every document is an important moment. We want the process around those moments to feel easier to manage.</p>
            </div>
            <ol class="about-story-timeline">
                <li>
                    <span class="about-step" aria-hidden="true">01</span>
                    <article class="about-story-card">
                        <h3>The Beginning — A Clear Purpose</h3>
                        <p>We began with a simple vision: make document-related procedures more accessible, organised, and convenient for people navigating administrative requirements.</p>
                    </article>
                </li>
                <li>
                    <span class="about-step" aria-hidden="true">02</span>
                    <article class="about-story-card">
                        <h3>Growing Through Trust</h3>
                        <p>As Dubai grew as an international business and residential destination, efficient documentation support became increasingly important. We focus on understanding each client’s needs, explaining processes clearly, and respecting their time.</p>
                    </article>
                </li>
                <li>
                    <span class="about-step" aria-hidden="true">03</span>
                    <article class="about-story-card">
                        <h3>Today — Your Support Partner</h3>
                        <p>We aim to support individuals and businesses with notary-related assistance, legal documentation, document clearing, and guidance on applicable procedures.</p>
                    </article>
                </li>
                <li>
                    <span class="about-step" aria-hidden="true">04</span>
                    <article class="about-story-card">
                        <h3>Looking Ahead</h3>
                        <p>We continue working to improve our service through professionalism, transparency, responsiveness, and consistent attention to detail.</p>
                    </article>
                </li>
            </ol>
        </div>
    </section>

    <section class="section section-soft about-services">
        <div class="container">
            <div class="about-section-heading">
                <span class="eyebrow">What we do</span>
                <h2>Documentation Support for Different Needs</h2>
                <p>We provide assistance and guidance across a range of documentation-related services.</p>
            </div>
            <ul class="about-service-list">
                <li><span aria-hidden="true">01</span>Notary-related services and document notarisation assistance</li>
                <li><span aria-hidden="true">02</span>Legal document preparation and processing support</li>
                <li><span aria-hidden="true">03</span>Power of Attorney (POA) documentation assistance</li>
                <li><span aria-hidden="true">04</span>MOJ-related documentation and legal translation coordination</li>
                <li><span aria-hidden="true">05</span>Document attestation and authentication guidance</li>
                <li><span aria-hidden="true">06</span>Business, corporate, and commercial documentation</li>
                <li><span aria-hidden="true">07</span>Government-related document clearing and administrative support</li>
            </ul>
            <p class="about-disclaimer">Service availability, requirements, and procedures depend on the document type and the relevant authority.</p>
        </div>
    </section>

    <section class="section about-values">
        <div class="container">
            <div class="about-section-heading">
                <span class="eyebrow">Why choose us</span>
                <h2>Support That Starts With Understanding</h2>
            </div>
            <div class="about-values-grid">
                <article class="about-value-card about-value-featured"><span aria-hidden="true">01</span><div><h3>A Client-First Approach</h3><p>We take time to understand your requirements and help you navigate the process with greater clarity.</p></div></article>
                <article class="about-value-card"><span aria-hidden="true">02</span><h3>Professional &amp; Reliable Support</h3><p>We value accuracy, responsibility, and attention to detail in every interaction.</p></article>
                <article class="about-value-card"><span aria-hidden="true">03</span><h3>Clear Communication</h3><p>We aim to explain procedures, requirements, and next steps in a straightforward way.</p></article>
                <article class="about-value-card"><span aria-hidden="true">04</span><h3>Convenient Dubai Location</h3><p>Our office at Almas Tower, Dubai Lake Avenue, is conveniently located for clients seeking documentation support in Dubai.</p></article>
                <article class="about-value-card"><span aria-hidden="true">05</span><h3>Commitment to Confidentiality</h3><p>Documents can contain sensitive personal and business information. We treat client information with care and respect.</p></article>
            </div>
        </div>
    </section>

    <section class="about-purpose">
        <div class="container about-purpose-grid">
            <article>
                <span class="eyebrow eyebrow-light">Our mission</span>
                <h2>Make complex procedures easier to navigate.</h2>
                <p>To simplify document-related procedures through accessible, professional, and dependable support that saves clients time.</p>
            </article>
            <article>
                <span class="eyebrow eyebrow-light">Our vision</span>
                <h2>Be a dependable name in Dubai.</h2>
                <p>To become a trusted and recognised name for document clearing and documentation support, known for professionalism, integrity, and client satisfaction.</p>
            </article>
        </div>
    </section>

    <section class="section about-contact-section">
        <div class="container about-contact-card">
            <div class="about-contact-copy">
                <span class="eyebrow">Let’s make your documentation easier</span>
                <h2>Take the next step with confidence.</h2>
                <p>Whether you need assistance with personal documents, legal paperwork, or business-related procedures, we are here to help you understand the process.</p>
                <a class="button button-gold" href="{{ route('contact') }}">Contact us today <span aria-hidden="true">→</span></a>
            </div>
            <address class="about-contact-details">
                <strong>{{ $siteSettings['site_name'] }}</strong>
                <span>{{ $siteSettings['site_address'] }}</span>
                @if ($siteSettings['site_phone'])
                    <a data-contact-method="phone" href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings['site_phone']) }}">{{ $siteSettings['site_phone'] }}</a>
                @endif
            </address>
        </div>
    </section>
@endsection
