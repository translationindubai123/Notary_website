@extends('admin.layouts.app')
@section('title', 'Inquiry from '.$inquiry->name)
@section('content')
<div class="admin-title"><div><span class="eyebrow">Contact inquiry</span><h1>Message details</h1><p>Received {{ $inquiry->created_at->format('Y-m-d H:i') }}</p></div><a class="text-link" href="{{ route('admin.inquiries.index') }}">← All inquiries</a></div>
<section class="admin-message">
    <dl>
        <div><dt>Name</dt><dd>{{ $inquiry->name }}</dd></div>
        <div><dt>Email</dt><dd><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></dd></div>
        @if ($inquiry->phone)<div><dt>Phone</dt><dd><a href="tel:{{ preg_replace('/[^0-9+]/', '', $inquiry->phone) }}">{{ $inquiry->phone }}</a></dd></div>@endif
        <div><dt>Service of interest</dt><dd>@if ($inquiry->service)<a href="{{ route('admin.services.edit', $inquiry->service) }}">{{ $inquiry->service->title }}</a>@else Not selected or no longer available @endif</dd></div>
        <div><dt>Subject</dt><dd>{{ $inquiry->subject ?: 'Not provided' }}</dd></div>
        <div><dt>Message</dt><dd class="admin-message-body">{{ $inquiry->message }}</dd></div>
    </dl>
    <a class="button button-dark" href="mailto:{{ $inquiry->email }}?subject={{ rawurlencode('Re: '.($inquiry->subject ?: 'Website inquiry')) }}">Reply by email</a>
</section>
@endsection
