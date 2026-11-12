@extends('layouts.app')
@php
    $mojFaqs = [
        [
            'question' => 'What MOJ services are available in Dubai?',
            'answer' => 'The UAE Ministry of Justice provides a range of official services relating to notarial procedures, contracts and deeds, legal translators, and other judicial and administrative matters. The appropriate service depends on your document type and requirements. Some related services, such as MOFA attestation, are handled by separate authorities.',
        ],
        [
            'question' => 'How can I apply for MOJ services in Dubai?',
            'answer' => 'Start by identifying the service you require and reviewing its eligibility criteria, supporting documents and application procedure. Some UAE Ministry of Justice services offer electronic applications, while others may require additional verification or specific formalities. Always confirm the current requirements through the relevant official service.',
        ],
        [
            'question' => 'What is MOJ-certified legal translation in Dubai?',
            'answer' => "MOJ-certified legal translation generally refers to a translation completed by a legal translator authorised or registered by the relevant UAE authority for the applicable purpose. It may be required for contracts, court documents, Powers of Attorney, company records and other legal documents. Acceptance depends on the receiving authority's requirements.",
        ],
        [
            'question' => 'How much does MOJ legal translation cost in Dubai?',
            'answer' => 'The cost of legal translation in Dubai depends on the language pair, document length, complexity and applicable service fees. Additional charges may apply for urgent processing or related services. Request a quotation based on your document and confirm whether the receiving authority requires an authorised legal translator.',
        ],
        [
            'question' => 'What documents are required for MOJ services?',
            'answer' => 'Requirements vary by service. Depending on the procedure, you may need a valid Emirates ID or passport, the original document, supporting certificates, related legal instruments or an authorised translation. Check the individual service requirements before submitting an application.',
        ],
        [
            'question' => 'What is the difference between MOJ attestation and MOFA attestation?',
            'answer' => "The UAE Ministry of Justice and the Ministry of Foreign Affairs have different responsibilities and procedures. MOJ services cover matters within the Ministry's remit, while MOFA provides official document attestation through its designated process. The correct procedure depends on the document, where it was issued and where it will be used.",
        ],
        [
            'question' => 'Can I get MOJ services online in the UAE?',
            'answer' => 'Some Ministry of Justice services are available through electronic channels, subject to eligibility and the requirements of the specific service. Online availability does not apply to every document or procedure. Check the official service page to confirm whether you can submit your request electronically.',
        ],
        [
            'question' => 'Can foreign documents be used in the UAE without attestation?',
            'answer' => 'Some foreign-issued documents require authentication or attestation before they can be accepted in the UAE. Requirements depend on the country of origin, document type and intended use. Certain documents may also require legal translation. Confirm the required process with the authority or institution receiving the document.',
        ],
        [
            'question' => 'Do I need MOJ-certified translation for documents submitted to UAE courts?',
            'answer' => "Some documents submitted to UAE courts require translation by a legal translator authorised for the relevant purpose. The language, format and certification requirements depend on the document and the court's instructions. Confirm the requirements before arranging the translation.",
        ],
        [
            'question' => 'What is the difference between notarisation and legal translation?',
            'answer' => "Notarisation is a formal legal procedure performed by an authorised notary, such as attesting a signature or handling an eligible legal instrument. Legal translation converts a document into another language for legal or official use. A document may require both procedures, depending on the receiving authority's requirements.",
        ],
        [
            'question' => 'Can the UAE Ministry of Justice authenticate contracts and legal deeds?',
            'answer' => 'The Ministry of Justice provides specified services for writing and authenticating eligible contracts and deeds. Whether a particular document qualifies depends on the instrument, the parties involved and the applicable requirements. Review the relevant official service before applying.',
        ],
        [
            'question' => 'How can I find the right MOJ service for my document?',
            'answer' => 'Use the MOJ Services directory to search for your document type or browse the available categories. Review the service description and supporting-document requirements. If you are unsure whether your document needs legal translation, notarisation, authentication or attestation, confirm the appropriate procedure with the competent authority before proceeding.',
        ],
    ];
    $mojServiceTopics = [
        ['title' => 'Legal translation', 'description' => 'MOJ-certified legal translation and legal translator information.', 'query' => 'legal translation'],
        ['title' => 'Official deed authentication', 'description' => 'Explore information about eligible deeds and authentication procedures.', 'query' => 'deed'],
        ['title' => 'Contracts and deeds', 'description' => 'Search for information about contracts, deeds and related procedures.', 'query' => 'contract'],
        ['title' => 'Affidavits', 'description' => 'Find service information for affidavits and related legal documents.', 'query' => 'affidavit'],
        ['title' => 'Legal translators', 'description' => 'Look for information related to legal translators and translation services.', 'query' => 'translator'],
        ['title' => 'Attestation guidance', 'description' => 'Search for document attestation and authentication information.', 'query' => 'attestation'],
    ];
    $mojFaqData = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($mojFaqs)->map(function ($faq) {
            return [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
            ];
        })->values()->all(),
    ];
@endphp
@section('title', 'MOJ Services in Dubai | Legal Translation & Documents')
@section('meta_description', 'Explore MOJ-related services in Dubai, including MOJ-certified legal translation, document authentication, attestation guidance and eligible legal document procedures.')
@section('canonical', route('moj'))
@php($breadcrumbs = [['label' => 'Home', 'url' => route('home')], ['label' => 'MOJ Services']])
@push('head')
    <script type="application/ld+json">@json($mojFaqData)</script>
@endpush
@section('content')
    <section class="page-hero moj-directory-hero">
        <div class="container moj-directory-hero-inner">
            <div class="moj-directory-intro">
                <span class="eyebrow">MOJ-related services in Dubai</span>
                <h1>MOJ Services in Dubai</h1>
                <p class="moj-directory-lead">Explore official Ministry of Justice service information and related legal document procedures in Dubai. Search for a document or service, or browse the available categories to understand possible next steps.</p>
                @if ($siteSettings['moj_introduction'])
                    <div class="moj-directory-introduction"><p>{{ $siteSettings['moj_introduction'] }}</p></div>
                @endif
            </div>
            <aside class="moj-directory-note">
                <span class="directory-note-mark" aria-hidden="true">M</span>
                <span class="eyebrow">Service directory</span>
                <h2>Find information for your document</h2>
                <p>Requirements depend on the service, document and receiving authority. Check the individual service details and confirm current requirements before applying.</p>
            </aside>
        </div>
        <div class="container moj-search-panel" id="moj-service-search">
            <div><span class="eyebrow">Search MOJ services</span><h2>What service or document are you looking for?</h2><p>Enter a document name or keyword, or narrow your search to a category.</p></div>
            <form class="search-form" action="{{ route('moj.services.index') }}" method="get">
                <label class="visually-hidden" for="moj-service-query">Search MOJ services</label>
                <input id="moj-service-query" name="q" type="search" value="{{ request('q') }}" placeholder="e.g. legal translation, deed, affidavit">
                <label class="visually-hidden" for="moj-service-category">Filter by MOJ category</label>
                <select id="moj-service-category" name="category">
                    <option value="">All categories</option>
                    @foreach ($searchCategories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button class="button button-dark" type="submit">Search services <span aria-hidden="true">→</span></button>
            </form>
        </div>
    </section>

    <section class="section moj-category-section" id="moj-service-categories">
        <div class="container">
            <div class="section-heading section-heading-intro">
                <div><span class="eyebrow">Browse the directory</span><h2>MOJ Services by Category</h2><p>Browse current MOJ service categories, including legal translation, deeds, contracts and other available procedures. Category listings come from the published service directory.</p></div>
                <a class="text-link" href="{{ route('moj.services.index') }}">View all MOJ services <span aria-hidden="true">→</span></a>
            </div>
            @if ($categories->isNotEmpty())
                <div class="category-grid">@foreach ($categories as $index => $category)<x-category-card :category="$category" :index="$index" />@endforeach</div>
            @endif
            <div class="moj-topic-heading"><span class="eyebrow">Common service areas</span><h3>Search by document or topic</h3></div>
            <div class="moj-topic-grid">
                @foreach ($mojServiceTopics as $topic)
                    <a href="{{ route('moj.services.index', ['q' => $topic['query']]) }}"><span>{{ $topic['title'] }}</span><small>{{ $topic['description'] }}</small><span class="moj-topic-link">Search services <span aria-hidden="true">→</span></span></a>
                @endforeach
            </div>
            @if ($relatedServices->isNotEmpty())
                <div class="moj-category-services">
                    <div class="section-heading"><div><span class="eyebrow">Published service information</span><h2>Explore MOJ-Related Services</h2></div><a class="text-link" href="{{ route('moj.services.index') }}">All MOJ services <span aria-hidden="true">→</span></a></div>
                    <div class="service-grid">@foreach ($relatedServices as $service)<x-service-card :service="$service" />@endforeach</div>
                </div>
            @endif
        </div>
    </section>

    <section class="section section-soft moj-service-information">
        <div class="container moj-overview-layout">
            <div><span class="eyebrow">Understanding the Ministry's role</span><h2>What Are MOJ Services?</h2></div>
            <div class="moj-overview-copy">
                <p>The UAE Ministry of Justice provides official services within its remit, including specified judicial and administrative procedures, services related to contracts and deeds, and information concerning legal translators. Which service applies depends on the document, the parties and the intended use.</p>
                <p>Official Ministry services are provided through the competent government channels. Private businesses may offer assistance such as translation, document preparation or application support, but this assistance is separate from the Ministry and does not determine eligibility, approve an application or guarantee acceptance.</p>
                <p>Some related procedures, including MOFA attestation, are handled by other authorities. Confirm the correct process and current requirements with the authority that will receive or process your document.</p>
                @if ($siteSettings['moj_body'])
                    <div class="moj-copy">{!! nl2br(e($siteSettings['moj_body'])) !!}</div>
                @endif
            </div>
        </div>
    </section>

    <section class="section moj-howto" id="how-to-find-moj-service">
        <div class="container">
            <div class="section-heading section-heading-intro"><div><span class="eyebrow">A practical starting point</span><h2>How to Find the Right MOJ Service</h2><p>Use these steps to identify relevant service information and prepare to confirm the official requirements.</p></div></div>
            <ol class="moj-howto-list">
                <li><span>01</span><div><h3>Identify your document</h3><p>Determine whether you have a contract, deed, affidavit, court document, translation or another legal record.</p></div></li>
                <li><span>02</span><div><h3>Clarify its purpose</h3><p>Note where the document was issued, where it will be used and which authority or institution will receive it.</p></div></li>
                <li><span>03</span><div><h3>Search the directory</h3><p>Search by document name or keyword, or browse the MOJ categories for a potentially relevant service.</p></div></li>
                <li><span>04</span><div><h3>Review eligibility</h3><p>Read the official service description and check who may apply, what documents are needed and any stated conditions.</p></div></li>
                <li><span>05</span><div><h3>Check translation or authentication</h3><p>Confirm whether the document needs legal translation, notarisation, authentication or attestation, and which authority is responsible.</p></div></li>
                <li><span>06</span><div><h3>Confirm the current procedure</h3><p>Check the application channel, fees, supporting records and any verification steps directly with the competent authority before submitting.</p></div></li>
            </ol>
        </div>
    </section>

    <section class="section section-soft moj-distinctions">
        <div class="container">
            <div class="section-heading section-heading-intro"><div><span class="eyebrow">Choose the right procedure</span><h2>Translation, Authentication and Attestation</h2><p>These terms describe different services. A document may need one or more procedures, depending on its origin, purpose and receiving authority.</p></div></div>
            <div class="moj-difference-grid">
                <article><span>01</span><h3>Legal translation</h3><p>A legal translator translates a document for legal or official use. Check whether the receiving authority requires a translator authorised for that purpose, and confirm its language and certification requirements.</p></article>
                <article><span>02</span><h3>Authentication</h3><p>Authentication verifies or confirms an aspect of a document or signature through the relevant procedure. The responsible authority and process depend on the document and its intended use.</p></article>
                <article><span>03</span><h3>Attestation</h3><p>Attestation is an official process carried out by the authority responsible for that step, such as MOFA for its designated attestation services. It is distinct from legal translation and other MOJ procedures.</p></article>
                <article><span>04</span><h3>Notarisation</h3><p>Notarisation is a formal act performed by an authorised notary, such as attesting a signature or handling an eligible instrument. It is not the same as translating or attesting a document.</p></article>
            </div>
            <p class="moj-directory-disclaimer">Always confirm the required procedure with the authority or institution receiving the document. Service information on this website does not imply government affiliation or endorsement.</p>
        </div>
    </section>

    <section class="section moj-faq" id="moj-faq">
        <div class="container">
            <div class="section-heading section-heading-intro"><div><span class="eyebrow">MOJ service information</span><h2>Frequently Asked Questions About MOJ Services in Dubai</h2><p>General answers to common questions. Specific eligibility and requirements depend on the relevant official service.</p></div></div>
            <div class="faq-list">
                @foreach ($mojFaqs as $faq)
                    <details><summary>{{ $faq['question'] }}</summary><p>{{ $faq['answer'] }}</p></details>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section moj-directory-cta">
        <div class="container moj-directory-cta-inner">
            <div><span class="eyebrow eyebrow-light">Next steps</span><h2>Find the service information for your document.</h2><p>Search the MOJ directory, review the relevant service details and confirm current requirements with the competent authority. If you need help finding information on this website, contact us.</p></div>
            <div class="moj-directory-cta-actions">
                <a class="button button-light" href="#moj-service-search">Search MOJ services <span aria-hidden="true">↑</span></a>
                <a class="moj-directory-contact-link" href="{{ route('contact') }}">Contact us</a>
            </div>
        </div>
    </section>
@endsection
