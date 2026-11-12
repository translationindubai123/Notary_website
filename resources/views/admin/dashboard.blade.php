@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="admin-title"><div><span class="eyebrow">Overview</span><h1>Dashboard</h1><p>Manage service content and categories.</p></div></div>
<div class="admin-stats"><div><span>Categories</span><strong>{{ $categoryCount }}</strong></div><div><span>Services</span><strong>{{ $serviceCount }}</strong></div><div><span>Published</span><strong>{{ $publishedCount }}</strong></div><div><span>Inquiries</span><strong>{{ $inquiryCount }}</strong></div></div>
<div class="admin-actions"><a class="button button-gold" href="{{ route('admin.services.create', ['type' => 'notary']) }}">Add a notary service</a><a class="button button-outline" href="{{ route('admin.categories.create', ['type' => 'notary']) }}">Add a notary category</a><a class="button button-outline" href="{{ route('admin.services.create', ['type' => 'moj']) }}">Add an MOJ service</a><a class="button button-outline" href="{{ route('admin.categories.create', ['type' => 'moj']) }}">Add an MOJ category</a><a class="button button-outline" href="{{ route('admin.inquiries.index') }}">View inquiries</a><a class="button button-outline" href="{{ route('admin.settings.edit') }}">Edit site settings</a><a class="button button-outline" href="{{ route('home') }}">View public website</a></div>
<p class="admin-note">Content entered as a draft remains hidden from public pages and the sitemap until it is published.</p>
@endsection
