@extends('admin.layouts.app')
@section('title', 'Site settings')
@section('content')
<div class="admin-title"><div><span class="eyebrow">Website content</span><h1>Site settings</h1><p>Business information and homepage content.</p></div></div>
<form class="admin-form" method="post" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
    @csrf @method('put')
    <div class="admin-form-section"><h2>Business details</h2><p class="admin-note">Only enter real, approved business contact information. Leave optional fields blank if they are not available.</p><div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="site_name">Business name</label><input class="form-control" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? config('site.name')) }}" required maxlength="180"></div>
        <div class="col-md-6"><label class="form-label" for="site_description">Site description</label><input class="form-control" id="site_description" name="site_description" value="{{ old('site_description', $settings['site_description'] ?? config('site.description')) }}" maxlength="320"></div>
        <div class="col-md-6"><label class="form-label" for="site_phone">Phone</label><input class="form-control" id="site_phone" name="site_phone" value="{{ old('site_phone', $settings['site_phone'] ?? config('site.phone')) }}" maxlength="60"></div>
        <div class="col-md-6"><label class="form-label" for="site_whatsapp">WhatsApp number</label><input class="form-control" id="site_whatsapp" name="site_whatsapp" value="{{ old('site_whatsapp', $settings['site_whatsapp'] ?? config('site.whatsapp')) }}" maxlength="40"><div class="form-text">Include country code; digits, +, spaces, parentheses, dots and hyphens are accepted.</div></div>
        <div class="col-md-6"><label class="form-label" for="site_email">Public email</label><input class="form-control" id="site_email" type="email" name="site_email" value="{{ old('site_email', $settings['site_email'] ?? config('site.email')) }}" maxlength="254"></div>
        <div class="col-md-6"><label class="form-label" for="site_inquiries_email">Inquiry notification recipient</label><input class="form-control" id="site_inquiries_email" type="email" name="site_inquiries_email" value="{{ old('site_inquiries_email', $settings['site_inquiries_email'] ?? config('site.inquiries_email')) }}" maxlength="254"></div>
        <div class="col-md-6"><label class="form-label" for="site_address">Business address</label><textarea class="form-control" id="site_address" name="site_address" rows="3" maxlength="1000">{{ old('site_address', $settings['site_address'] ?? config('site.address')) }}</textarea></div>
        <div class="col-md-6"><label class="form-label" for="site_office_hours">Office hours</label><textarea class="form-control" id="site_office_hours" name="site_office_hours" rows="3" maxlength="500">{{ old('site_office_hours', $settings['site_office_hours'] ?? config('site.office_hours')) }}</textarea></div>
        <div class="col-12"><label class="form-label" for="site_logo">Website logo</label><input class="form-control" id="site_logo" type="file" name="site_logo" accept=".jpg,.jpeg,.png,.webp"><div class="form-text">JPG, PNG, or WebP; maximum 2 MB. Use a transparent PNG or WebP for the best fit.</div>
            @if (!empty($settings['site_logo']))
                <div class="site-logo-setting-preview"><img src="{{ asset('storage/'.$settings['site_logo']) }}" alt="Current website logo"><label class="form-check"><input class="form-check-input" type="checkbox" name="remove_site_logo" value="1"><span class="form-check-label">Remove current logo</span></label></div>
            @endif
        </div>
    </div></div>
    <div class="admin-form-section"><h2>Homepage hero and search metadata</h2><p class="admin-note">Keep the copy accurate to the services actually offered. The SEO title uses the current business name automatically.</p><div class="row g-3">
        <div class="col-12"><label class="form-label" for="home_hero_eyebrow">Hero label</label><input class="form-control" id="home_hero_eyebrow" name="home_hero_eyebrow" value="{{ old('home_hero_eyebrow', $settings['home_hero_eyebrow'] ?? 'PROFESSIONAL DOCUMENT SERVICES · DUBAI, UAE') }}" required maxlength="120"></div>
        <div class="col-12"><label class="form-label" for="home_hero_title">Hero heading</label><input class="form-control" id="home_hero_title" name="home_hero_title" value="{{ old('home_hero_title', $settings['home_hero_title'] ?? 'Notary Services in Dubai for Personal and Business Documents') }}" required maxlength="180"></div>
        <div class="col-12"><label class="form-label" for="home_hero_introduction">Hero introduction</label><textarea class="form-control" id="home_hero_introduction" name="home_hero_introduction" rows="3" required maxlength="500">{{ old('home_hero_introduction', $settings['home_hero_introduction'] ?? 'Find the right notary service for your document. Explore powers of attorney, company documents, legal notices and other notarial services, with clear information to help you understand the next steps.') }}</textarea></div>
        <div class="col-12"><label class="form-label" for="home_meta_description">Homepage meta description</label><textarea class="form-control" id="home_meta_description" name="home_meta_description" rows="2" required maxlength="320">{{ old('home_meta_description', $settings['home_meta_description'] ?? 'Looking for notary services in Dubai? Explore document notarisation, powers of attorney, company documents, legal notices and other notary services. Contact our team for guidance.') }}</textarea></div>
        <div class="col-md-6"><label class="form-label" for="home_primary_cta">Primary button label</label><input class="form-control" id="home_primary_cta" name="home_primary_cta" value="{{ old('home_primary_cta', $settings['home_primary_cta'] ?? 'Explore Our Services') }}" required maxlength="60"></div>
        <div class="col-md-6"><label class="form-label" for="home_secondary_cta">Secondary button label</label><input class="form-control" id="home_secondary_cta" name="home_secondary_cta" value="{{ old('home_secondary_cta', $settings['home_secondary_cta'] ?? 'Contact Our Team') }}" required maxlength="60"></div>
    </div></div>
    <p class="admin-note">Text is escaped on public pages. SMTP notifications are sent only when SMTP and a recipient are configured.</p>
    <button class="button button-dark" type="submit">Save site settings</button>
</form>
@endsection
