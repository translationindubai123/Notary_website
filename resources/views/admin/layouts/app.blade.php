<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | {{ $siteSettings['site_name'] }}</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-body">
<header class="admin-header"><div class="container admin-header-inner">
    <a class="brand" href="{{ route('admin.dashboard') }}">{{ $siteSettings['site_name'] }} <span>Admin</span></a>
    @auth
        <nav class="admin-nav" aria-label="Admin navigation">
            <a href="{{ route('admin.dashboard') }}" @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif>Dashboard</a>
            <a href="{{ route('admin.categories.index', ['type' => 'notary']) }}" @if (request()->routeIs('admin.categories.*') && request('type', 'notary') === 'notary') aria-current="page" @endif>Notary categories</a>
            <a href="{{ route('admin.services.index', ['type' => 'notary']) }}" @if (request()->routeIs('admin.services.*') && request('type', 'notary') === 'notary') aria-current="page" @endif>Notary services</a>
            <a href="{{ route('admin.categories.index', ['type' => 'moj']) }}" @if (request()->routeIs('admin.categories.*') && request('type') === 'moj') aria-current="page" @endif>MOJ categories</a>
            <a href="{{ route('admin.services.index', ['type' => 'moj']) }}" @if (request()->routeIs('admin.services.*') && request('type') === 'moj') aria-current="page" @endif>MOJ services</a>
            <a href="{{ route('admin.inquiries.index') }}" @if (request()->routeIs('admin.inquiries.*')) aria-current="page" @endif>Inquiries</a>
            <a href="{{ route('admin.settings.edit') }}" @if (request()->routeIs('admin.settings.*')) aria-current="page" @endif>Site settings</a>
        </nav>
        <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="admin-logout" type="submit">Sign out</button></form>
    @endauth
</div></header>
<main class="container admin-main">
    @if (session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger" role="alert"><strong>Please review the following:</strong><ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</main>
</body></html>
