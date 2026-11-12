@extends('layouts.app')
@php
    $isNotaryDirectory = $serviceType !== 'moj';
    $notaryFaqs = [
        [
            'question' => 'What types of documents can be notarised in Dubai?',
            'answer' => "Eligible documents may include Powers of Attorney, declarations, affidavits, agreements, company instruments, legal notices and other formal legal documents. The available procedure depends on the document type and the applicable authority's requirements.",
        ],
        [
            'question' => 'How do I find a specific notary service in Dubai?',
            'answer' => 'Use the service search to enter a document name or keyword, or browse the available categories. Open the relevant service page to review its purpose and requirements. If you cannot identify the correct category, contact the appropriate service provider for guidance.',
        ],
        [
            'question' => 'Can I get a Power of Attorney notarised in Dubai?',
            'answer' => "Eligible Powers of Attorney may be processed through the applicable notary channel. The requirements depend on the powers being granted, the parties involved and the intended use of the document.",
        ],
        [
            'question' => 'Can notary services in Dubai be completed online?',
            'answer' => "Certain notarial procedures may be available electronically, subject to eligibility and the relevant authority's requirements. Other requests may require in-person attendance or additional verification. Confirm the available route for your specific document.",
        ],
        [
            'question' => 'How much do notary services cost in Dubai?',
            'answer' => 'The cost depends on the type of instrument, the applicable government fee schedule and any additional services required. Check the current fees with the competent authority or authorised provider before proceeding.',
        ],
        [
            'question' => 'What is the difference between a notary public and a legal translator?',
            'answer' => 'A notary public performs authorised notarial acts, such as registering instruments or attesting signatures. A legal translator translates documents for legal or official use in accordance with the applicable requirements. These are different professional functions, although a document may require both.',
        ],
        [
            'question' => 'Do documents for use outside the UAE need additional authentication?',
            'answer' => 'Some documents intended for use outside the UAE require additional authentication, attestation, legalisation or translation. The requirements depend on the destination country, document type and receiving institution. Confirm the required process before submitting the document.',
        ],
        [
            'question' => 'What should I do if I cannot find my document in the service directory?',
            'answer' => 'Search using an alternative document name or a relevant keyword. You can also browse related categories or contact the appropriate service provider or authority to identify the applicable procedure. Do not assume that a similarly named service is legally equivalent to your document.',
        ],
    ];
    $notaryFaqData = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($notaryFaqs)->map(function ($faq) {
            return [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
            ];
        })->values()->all(),
    ];
@endphp
@section('title', $isNotaryDirectory ? 'Notary Services in Dubai | Document Notarisation' : 'MOJ Services | '.$siteSettings['site_name'])
@section('meta_description', $isNotaryDirectory ? 'Find notary services in Dubai for Power of Attorney, declarations, company documents, legal notices and other eligible legal instruments. Explore service types and document requirements.' : 'Browse MOJ service categories and service information.')
@section('canonical', route($isNotaryDirectory ? 'services.index' : 'moj.services.index'))
@section('robots', request()->query() ? 'noindex,follow' : 'index,follow')
@php($breadcrumbs = [['label' => 'Home', 'url' => route('home')], ['label' => $isNotaryDirectory ? 'Notary Services' : 'MOJ Services']])
@if ($isNotaryDirectory)
    @push('head')
        <script type="application/ld+json">@json($notaryFaqData)</script>
    @endpush
@endif
@section('content')
    @if ($isNotaryDirectory)
        <section class="page-hero notary-directory-hero">
            <div class="container notary-directory-hero-inner">
                <div class="notary-directory-intro">
                    <span class="eyebrow">Notary services in Dubai</span>
                    <h1>Notary Services in Dubai</h1>
                    <h2>Explore Our Notary Services and Document Requirements</h2>
                    <p>Finding the right notary public in Dubai starts with understanding the document you need to prepare, sign, authenticate or register. Whether you require a Power of Attorney, a declaration, a company agreement, a legal notice or another legal instrument, choosing the appropriate service helps you understand the applicable requirements and next steps.</p>
                    <p>Explore our notary service directory to find relevant document types, review available service information and identify the procedure that may apply to your request.</p>
                    <p class="notary-directory-prompt">Search for a service or browse by category to get started.</p>
                    <div class="hero-actions">
                        <a class="button button-dark" href="#notary-service-search">Search for a Notary Service</a>
                        <a class="button button-outline" href="#notary-service-categories">Browse All Services</a>
                    </div>
                </div>
                <aside class="notary-directory-note">
                    <span class="directory-note-mark" aria-hidden="true">N</span>
                    <span class="eyebrow">A useful starting point</span>
                    <h2>Start with your document</h2>
                    <p>Identify what the document is for, who will use it and where it will be submitted. These details can help you find relevant service information.</p>
                    <a class="text-link" href="#how-to-find-a-notary-service">See how to get started <span aria-hidden="true">→</span></a>
                </aside>
            </div>
            <div class="container"><p class="notary-directory-disclaimer">Service availability, documentation requirements, fees and processing procedures vary according to the document, the parties involved and the relevant authority.</p></div>
        </section>

        <section class="section notary-directory-listing" id="notary-service-categories">
            <div class="container">
                <div class="section-heading section-heading-intro notary-directory-heading">
                    <div><span class="eyebrow">Browse the directory</span><h2>Explore Notary Services by Category</h2><p>Notarial requirements differ depending on whether a document concerns personal affairs, business transactions, legal representation or another formal matter. Browse the categories below to find relevant services and more detailed information.</p></div>
                </div>
                @if ($categories->isNotEmpty())
                    <nav class="category-shortcuts notary-category-shortcuts" aria-label="Browse notary service categories">
                        @foreach ($categories->whereNull('parent_id') as $category)
                            <a href="{{ $category->publicCategoryUrl() }}">{{ $category->name }}</a>
                        @endforeach
                    </nav>
                @endif
                <div class="notary-search-panel" id="notary-service-search">
                    <div><span class="eyebrow">Find a document or service</span><h2>Search the notary service directory</h2><p>Enter a document name or keyword, or narrow results to a category.</p></div>
                    <form class="search-form" action="{{ route('services.index') }}" method="get">
                        <label class="visually-hidden" for="service-search">Search notary services</label><input id="service-search" name="q" type="search" value="{{ request('q') }}" placeholder="e.g. Power of Attorney, company agreement">
                        <label class="visually-hidden" for="service-category">Filter by category</label><select id="service-category" name="category"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>@endforeach</select>
                        <button class="button button-dark" type="submit">Search services <span aria-hidden="true">→</span></button>
                    </form>
                </div>
                @if ($services->isNotEmpty())
                    <p class="result-count">{{ $services->total() }} {{ \Illuminate\Support\Str::plural('service', $services->total()) }} available</p>
                    <div class="service-grid notary-directory-service-grid">@foreach ($services as $service)<x-service-card :service="$service" />@endforeach</div>
                @else
                    <p class="empty-state">No published services match your search yet. Please try another term or contact us.</p>
                @endif
                <div class="pagination-wrap">{{ $services->links() }}</div>
            </div>
        </section>

        <section class="section section-soft notary-document-types">
            <div class="container">
                <div class="section-heading section-heading-intro"><div><span class="eyebrow">Common document types</span><h2>Notary Services for Personal and Business Documents</h2><p>Notary services may relate to a range of legal instruments. The directory includes available information for document types such as:</p></div></div>
                <div class="notary-document-type-grid">
                    <article><span aria-hidden="true">01</span><h3>Power of Attorney</h3><p>General or special authorisations for eligible property, business, vehicle, court and personal matters.</p></article>
                    <article><span aria-hidden="true">02</span><h3>Declarations and acknowledgments</h3><p>Declarations, affidavits, confirmations, acknowledgments of debt or receipt, and related instruments.</p></article>
                    <article><span aria-hidden="true">03</span><h3>Company and commercial documents</h3><p>Company agreements, Memoranda of Association, amendments, resolutions, share transfers and commercial arrangements.</p></article>
                    <article><span aria-hidden="true">04</span><h3>Legal notices and judicial documents</h3><p>Eligible judicial warnings, cancellation notices, publication notices and other formal legal instruments.</p></article>
                    <article><span aria-hidden="true">05</span><h3>Agreements and contracts</h3><p>Eligible sale, mortgage, settlement, family ownership and other agreements that may require a formal procedure.</p></article>
                    <article><span aria-hidden="true">06</span><h3>Personal, family and other documents</h3><p>Wills, family-related instruments, signature attestation and certified true copies, subject to applicable requirements.</p></article>
                </div>
            </div>
        </section>

        <section class="section notary-overview">
            <div class="container notary-overview-layout">
                <div><span class="eyebrow">Understanding the service</span><h2>What Are Notary Services in Dubai?</h2></div>
                <div class="notary-overview-copy">
                    <p>Notary services in Dubai involve formal legal procedures for eligible documents and instruments. Depending on the applicable law and the nature of the request, these procedures may include registering documents and contracts, attesting signatures, validating document dates and recording affidavits.</p>
                    <p>Notarial procedures can be relevant to personal transactions, business arrangements, legal representation, declarations and other matters where a formal instrument is required.</p>
                    <p>The appropriate procedure depends on the document's purpose, the identity and capacity of the parties, the supporting evidence and the requirements of the competent authority.</p>
                    <a class="text-link" href="#notary-service-search">Search the service directory <span aria-hidden="true">→</span></a>
                </div>
            </div>
        </section>

        <section class="section section-soft notary-howto" id="how-to-find-a-notary-service">
            <div class="container">
                <div class="section-heading section-heading-intro"><div><span class="eyebrow">A practical starting point</span><h2>How to Find the Right Notary Service</h2><p>Use these steps to narrow down relevant service information and understand what may need to be prepared.</p></div></div>
                <ol class="notary-howto-list">
                    <li><span>01</span><div><h3>Identify your document</h3><p>Determine whether you need a Power of Attorney, declaration, agreement, company document, legal notice, acknowledgment or another legal instrument.</p></div></li>
                    <li><span>02</span><div><h3>Confirm its purpose</h3><p>Consider why the document is required and where it will be used. Requirements may differ for a government authority, court, bank, company or overseas institution.</p></div></li>
                    <li><span>03</span><div><h3>Review the requirements</h3><p>Check which identification, supporting records, language arrangements and signing formalities may apply.</p></div></li>
                    <li><span>04</span><div><h3>Confirm the procedure</h3><p>Some documents may be eligible for electronic processing, while others may require attendance, verification or additional steps. Confirm the current procedure with the relevant authority or authorised provider.</p></div></li>
                    <li><span>05</span><div><h3>Confirm next steps</h3><p>Once you identify the likely service, confirm applicable fees, documents and processing arrangements before proceeding.</p></div></li>
                </ol>
            </div>
        </section>

        <section class="section notary-requirements">
            <div class="container notary-requirements-layout">
                <div><span class="eyebrow">Prepare for your request</span><h2>Documents Commonly Required for Notary Services</h2><p>The documents required for notarisation depend on the instrument and the circumstances of the request. Depending on the service, applicants may be asked to provide:</p></div>
                <div class="notary-requirements-card">
                    <ul>
                        <li>A valid Emirates ID, passport or other accepted identification</li>
                        <li>The document or draft requiring notarisation</li>
                        <li>Supporting agreements, certificates or official records</li>
                        <li>A valid Power of Attorney or other authorisation, where a representative is acting</li>
                        <li>Company documents and evidence of signing authority for corporate transactions</li>
                        <li>Relevant translations or additional authentication, where required</li>
                    </ul>
                    <p>This list is for general guidance only. Not every document is required for every service, and additional documents may be requested by the competent authority. Confirm current requirements for your specific document before submitting an application.</p>
                </div>
            </div>
        </section>

        <section class="section section-soft notary-differences">
            <div class="container">
                <div class="section-heading section-heading-intro"><div><span class="eyebrow">Know the difference</span><h2>Notarisation, Attestation and Legal Translation</h2><p>These terms are related, but they describe different procedures. Some documents may require more than one, depending on their destination and intended use.</p></div></div>
                <div class="notary-difference-grid">
                    <article><h3>Notarisation</h3><p>A formal legal procedure performed by an authorised notary within the scope of their authority. Depending on the instrument, it may involve registering a document, attesting a signature or carrying out another authorised notarial act.</p></article>
                    <article><h3>Document attestation</h3><p>Authentication or certification through the relevant authority or authorities. The required process depends on where the document was issued and where it will be used.</p></article>
                    <article><h3>Legal translation</h3><p>Translation of a document for legal or official use. The receiving authority may require a translator approved or certified by the relevant authority.</p></article>
                </div>
                <p class="notary-legal-note">Always confirm requirements with the receiving authority before proceeding.</p>
            </div>
        </section>

        <section class="section notary-fees">
            <div class="container notary-fees-inner">
                <span class="eyebrow">Plan your request</span><h2>Notary Service Fees and Processing Time</h2>
                <p>Notary service fees and processing times depend on the type of document, the applicable procedure, the number of signatures or pages where relevant, and any additional requirements.</p>
                <p>Some requests may involve government fees, translation costs, document preparation or further authentication. Electronic processing may be available for eligible documents, but it is not appropriate for every transaction.</p>
                <p>To understand the likely cost and timeframe, identify the document type first and confirm current requirements with the relevant authority or authorised service provider.</p>
                <a class="button button-dark" href="{{ route('contact') }}">Ask about your document <span aria-hidden="true">→</span></a>
            </div>
        </section>

        <section class="section section-soft notary-directory-faq">
            <div class="container">
                <div class="section-heading section-heading-intro"><div><span class="eyebrow">Notary service information</span><h2>Frequently Asked Questions About Notary Services in Dubai</h2><p>Answers to common questions about document types, procedures and requirements. Final requirements depend on the relevant authority.</p></div></div>
                <div class="faq-list">
                    @foreach ($notaryFaqs as $faq)
                        <details><summary>{{ $faq['question'] }}</summary><p>{{ $faq['answer'] }}</p></details>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="notary-directory-cta">
            <div class="container notary-directory-cta-inner">
                <div><span class="eyebrow eyebrow-light">Find the notary service you need</span><h2>Find the appropriate service and understand your next steps.</h2><p>Whether you need a Power of Attorney, a company agreement, a declaration, a legal notice or another eligible legal instrument, start by identifying the document and its intended purpose. Browse categories, search for the relevant document type and review the available information before proceeding.</p><p class="notary-directory-disclaimer">This information is for general guidance and does not constitute legal advice. Service availability, requirements, fees and acceptance of documents are determined by the competent authority and may vary according to the circumstances of each request.</p></div>
                <div class="notary-directory-cta-actions"><a class="button button-light" href="#notary-service-search">Search Notary Services <span aria-hidden="true">↑</span></a><a class="directory-contact-link" href="{{ route('contact') }}">Contact Us <span aria-hidden="true">→</span></a></div>
            </div>
        </section>
    @else
        <section class="page-hero"><div class="container"><span class="eyebrow">MOJ Services</span><h1>Explore our service information</h1><p>Search for a service or browse by category. Information and availability can vary by request.</p></div></section>
        <section class="section"><div class="container">
            @if ($categories->isNotEmpty())
                <nav class="category-shortcuts" aria-label="Browse service categories">
                    @foreach ($categories as $category)
                        <a href="{{ $category->publicCategoryUrl() }}">{{ $category->name }}</a>
                    @endforeach
                </nav>
            @endif
            <form class="search-form" action="{{ route('moj.services.index') }}" method="get">
                <label class="visually-hidden" for="service-search">Search services</label><input id="service-search" name="q" type="search" value="{{ request('q') }}" placeholder="Search MOJ services">
                <label class="visually-hidden" for="service-category">Filter by category</label><select id="service-category" name="category"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>@endforeach</select>
                <button class="button button-dark" type="submit">Search</button>
            </form>
            @if ($services->isNotEmpty())<p class="result-count">{{ $services->total() }} {{ \Illuminate\Support\Str::plural('service', $services->total()) }}</p><div class="service-grid">@foreach ($services as $service)<x-service-card :service="$service" />@endforeach</div>@else<p class="empty-state">No published services match your search yet. Please try another term or contact us.</p>@endif
            <div class="pagination-wrap">{{ $services->links() }}</div>
        </div></section>
    @endif
@endsection
