<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', e($siteSettings['site_name']))</title>
    <meta name="description" content="@yield('meta_description', e($siteSettings['site_description']))">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', e($siteSettings['site_name']))))">
    <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', e($siteSettings['site_description']))))">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta name="robots" content="@yield('robots', 'index,follow')">
    @php
        $mapSearch = $siteSettings['site_address'] ?? '';
        $mapEmbedUrl = $mapSearch !== ''
            ? 'https://maps.google.com/maps?q='.rawurlencode($mapSearch).'&output=embed'
            : null;
        $businessData = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            '@id' => rtrim(config('app.url'), '/').'#business',
            'name' => $siteSettings['site_name'],
            'url' => config('app.url'),
            'telephone' => $siteSettings['site_phone'],
            'email' => $siteSettings['site_email'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $siteSettings['site_address'],
                'addressLocality' => 'Dubai',
                'addressCountry' => 'AE',
            ],
        ];
        if ($mapEmbedUrl) {
            $businessData['hasMap'] = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($mapSearch);
        }
        $contactPageData = [
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            '@id' => route('contact').'#contact',
            'url' => route('contact'),
            'name' => 'Contact '.$siteSettings['site_name'],
            'mainEntity' => ['@id' => rtrim(config('app.url'), '/').'#business'],
        ];
    @endphp
    <script type="application/ld+json">@json($businessData)</script>
    @if (request()->routeIs('contact'))
        <script type="application/ld+json">@json($contactPageData)</script>
    @endif
    @if (isset($breadcrumbs) && count($breadcrumbs) > 0)
        @php
            $breadcrumbData = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => collect($breadcrumbs)->map(function ($breadcrumb, $index) use ($breadcrumbs) {
                    $item = [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $breadcrumb['label'],
                    ];
                    if ($index < count($breadcrumbs) - 1 && isset($breadcrumb['url'])) {
                        $item['item'] = $breadcrumb['url'];
                    }

                    return $item;
                })->all(),
            ];
        @endphp
        <script type="application/ld+json">@json($breadcrumbData)</script>
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    @stack('head')
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ route('home') }}">
                @if ($siteSettings['site_logo'])
                    <img class="brand-logo" src="{{ asset('storage/'.$siteSettings['site_logo']) }}" alt="" aria-hidden="true">
                @else
                    <span class="brand-mark" aria-hidden="true">N</span>
                @endif
                <span>{{ $siteSettings['site_name'] }}</span>
            </a>
            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-navigation" data-nav-toggle>
                <span>Menu</span><span class="nav-toggle-icon" aria-hidden="true"></span>
            </button>
            <nav class="main-nav" id="main-navigation" aria-label="Main navigation">
                <ul class="nav-list">
                    <li><a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>Home</a></li>
                    <li><a href="{{ rtrim(route('about'), '/').'/' }}" @if (request()->routeIs('about')) aria-current="page" @endif>About Us</a></li>
                    <li class="nav-services">
                        <div class="nav-services-heading">
                            <a class="nav-link" href="{{ route('services.index') }}" @if (request()->routeIs('services.*')) aria-current="page" @endif>Notary Services</a>
                            @if ($navigationCategories->isNotEmpty())
                                <button class="nav-submenu-toggle" type="button" aria-expanded="false" aria-controls="service-category-navigation" data-submenu-toggle>
                                    <span class="nav-chevron" aria-hidden="true"></span><span class="visually-hidden">Browse service categories</span>
                                </button>
                            @endif
                        </div>
                        @if ($navigationCategories->isNotEmpty())
                            <ul class="nav-dropdown" id="service-category-navigation">
                                @foreach ($navigationCategories as $navigationCategory)
                                    @include('components.navigation-category', ['category' => $navigationCategory])
                                @endforeach
                                <li class="nav-view-all"><a href="{{ route('services.index') }}">View all notary services</a></li>
                            </ul>
                        @endif
                    </li>
                    <li class="nav-services nav-moj-services">
                        <div class="nav-services-heading">
                            <a class="nav-link" href="{{ route('moj') }}" @if (request()->routeIs('moj*')) aria-current="page" @endif>MOJ Services</a>
                            @if ($mojNavigationCategories->isNotEmpty())
                                <button class="nav-submenu-toggle" type="button" aria-expanded="false" aria-controls="moj-category-navigation" data-submenu-toggle>
                                    <span class="nav-chevron" aria-hidden="true"></span><span class="visually-hidden">Browse MOJ service categories</span>
                                </button>
                            @endif
                        </div>
                        @if ($mojNavigationCategories->isNotEmpty())
                            <ul class="nav-dropdown" id="moj-category-navigation">
                                @foreach ($mojNavigationCategories as $navigationCategory)
                                    @include('components.navigation-category', ['category' => $navigationCategory])
                                @endforeach
                                <li class="nav-view-all"><a href="{{ route('moj.services.index') }}">View all MOJ services</a></li>
                            </ul>
                        @endif
                    </li>
                    <li><a href="{{ route('contact') }}" @if (request()->routeIs('contact*')) aria-current="page" @endif>Contact Us</a></li>
                </ul>
            </nav>
            <a class="button button-dark header-contact" href="{{ route('contact') }}">Make an inquiry <span aria-hidden="true">↗</span></a>
        </div>
    </header>
    <main id="main-content">
        @if (isset($breadcrumbs) && count($breadcrumbs))
            <div class="container"><nav class="breadcrumbs" aria-label="Breadcrumb">
                <ol>
                    @foreach ($breadcrumbs as $breadcrumb)
                        <li>
                            @if (!$loop->last && isset($breadcrumb['url']))
                                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                            @else
                                <span aria-current="page">{{ $breadcrumb['label'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav></div>
        @endif
        @if (session('status'))
            <div class="container"><div class="alert alert-success mt-3" role="status">{{ session('status') }}</div></div>
        @endif
        @if (session('notification_warning'))
            <div class="container"><div class="alert alert-warning mt-3" role="status">{{ session('notification_warning') }}</div></div>
        @endif
        @if ($errors->any())
            <div class="container"><div class="alert alert-danger mt-3" role="alert">
                <p class="mb-1">Please check the information below.</p>
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div></div>
        @endif
        @yield('content')
    </main>
    <footer class="site-footer">
        <div class="container footer-grid">
            <div><a class="brand brand-light" href="{{ route('home') }}">
                @if ($siteSettings['site_logo'])
                    <img class="brand-logo" src="{{ asset('storage/'.$siteSettings['site_logo']) }}" alt="" aria-hidden="true">
                @else
                    <span class="brand-mark" aria-hidden="true">N</span>
                @endif
                <span>{{ $siteSettings['site_name'] }}</span>
            </a><p class="footer-copy">Clear information and assistance for your notary service inquiries.</p></div>
            <div><h2>Explore</h2><a href="{{ route('services.index') }}">Notary Services</a><a href="{{ route('moj') }}">MOJ Services</a><a href="{{ rtrim(route('about'), '/').'/' }}">About Us</a><a href="{{ route('contact') }}">Contact</a></div>
            @if ($footerCategories->isNotEmpty())
                <div><h2>Service categories</h2>@foreach ($footerCategories as $footerCategory)<a href="{{ route('services.category', $footerCategory->slug) }}">{{ $footerCategory->name }}</a>@endforeach</div>
            @endif
            <div><h2>Contact</h2>
                @if ($siteSettings['site_phone'])<a data-contact-method="phone" href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings['site_phone']) }}">{{ $siteSettings['site_phone'] }}</a>@endif
                @if ($siteSettings['site_whatsapp'])<a data-contact-method="whatsapp" target="_blank" rel="noopener noreferrer" aria-label="Contact us on WhatsApp" href="https://wa.me/{{ preg_replace('/\D/', '', $siteSettings['site_whatsapp']) }}">WhatsApp</a>@endif
                @if ($siteSettings['site_email'])<a href="mailto:{{ $siteSettings['site_email'] }}">{{ $siteSettings['site_email'] }}</a>@endif
                @if ($siteSettings['site_address'])<p>{{ $siteSettings['site_address'] }}</p>@endif
                @if ($siteSettings['site_office_hours'])<p>{{ $siteSettings['site_office_hours'] }}</p>@endif
                @if (!$siteSettings['site_phone'] && !$siteSettings['site_email'] && !$siteSettings['site_address'])<a href="{{ route('contact') }}">Send an inquiry</a>@endif
            </div>
        </div>
        <div class="container footer-bottom"><span>© {{ now()->year }} {{ $siteSettings['site_name'] }}</span><a href="{{ route('sitemap') }}">Sitemap</a></div>
    </footer>
    <script src="{{ asset('js/site.js') }}" defer></script>
</body>
</html>
