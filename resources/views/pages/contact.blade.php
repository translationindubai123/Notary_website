@extends('layouts.app')
@section('title', 'Contact Us | '.$siteSettings['site_name'])
@section('meta_description', 'Contact our team with an inquiry about notary or MOJ services.')
@section('canonical', route('contact'))
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Contact Us'],
    ];
@endphp
@section('content')
    @php
        $mapSearch = $siteSettings['site_address'] ?? '';
        $mapEmbedUrl = $mapSearch !== ''
            ? 'https://maps.google.com/maps?q='.rawurlencode($mapSearch).'&output=embed'
            : null;
    @endphp
    <section class="page-hero"><div class="container narrow"><span class="eyebrow">Contact</span><h1>How can we help?</h1><p>Send us a message and our team will respond using the details you provide.</p></div></section>
    <section class="section"><div class="container contact-layout">
        <div class="contact-details"><span class="eyebrow">Get in touch</span><h2>We’re here to help with your inquiry.</h2>
            @if ($siteSettings['site_phone'])<p><strong>Phone</strong><br><a data-contact-method="phone" href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings['site_phone']) }}">{{ $siteSettings['site_phone'] }}</a></p>@endif
            @if ($siteSettings['site_whatsapp'])<p><strong>WhatsApp</strong><br><a data-contact-method="whatsapp" target="_blank" rel="noopener noreferrer" href="https://wa.me/{{ preg_replace('/\D/', '', $siteSettings['site_whatsapp']) }}">Start a WhatsApp conversation</a></p>@endif
            @if ($siteSettings['site_email'])<p><strong>Email</strong><br><a href="mailto:{{ $siteSettings['site_email'] }}">{{ $siteSettings['site_email'] }}</a></p>@endif
            @if ($siteSettings['site_address'])<p><strong>Address</strong><br>{{ $siteSettings['site_address'] }}</p>@endif
            @if ($siteSettings['site_office_hours'])<p><strong>Office hours</strong><br>{{ $siteSettings['site_office_hours'] }}</p>@endif
        </div>
        <x-contact-form :services="$services" :selected-service-id="$selectedServiceId" />
    </div></section>
    @if ($mapEmbedUrl)
        <section class="section contact-map-section" aria-labelledby="contact-map-title">
            <div class="container">
                <div class="contact-map-heading">
                    <div><span class="eyebrow">Find us in Dubai</span><h2 id="contact-map-title">Visit our office</h2>
                        @if ($siteSettings['site_address'])<p>{{ $siteSettings['site_address'] }}</p>@endif
                    </div>
                    <a class="button button-outline" href="https://www.google.com/maps/search/?api=1&amp;query={{ rawurlencode($mapSearch) }}" target="_blank" rel="noopener noreferrer">Get directions <span aria-hidden="true">↗</span></a>
                </div>
                <div class="contact-map-frame">
                    <iframe src="{{ $mapEmbedUrl }}" title="Map showing {{ $siteSettings['site_name'] }} location" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>
            </div>
        </section>
    @endif
@endsection
