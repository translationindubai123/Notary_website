@extends('admin.layouts.app')
@section('title', 'Inquiries')
@section('content')
<div class="admin-title"><div><span class="eyebrow">Contact</span><h1>Inquiries</h1><p>Messages submitted through the public contact form.</p></div></div>
<form class="admin-filter" method="get" action="{{ route('admin.inquiries.index') }}">
    <label class="visually-hidden" for="inquiry-search">Search inquiries</label>
    <input class="form-control" id="inquiry-search" type="search" name="q" value="{{ request('q') }}" placeholder="Search name, email or subject">
    <button class="button button-dark" type="submit">Search</button>
</form>
<div class="admin-table-wrap"><table class="table admin-table"><thead><tr><th>Sender</th><th>Service</th><th>Received</th><th>Subject</th><th></th></tr></thead><tbody>
@forelse ($inquiries as $inquiry)
<tr><td><strong>{{ $inquiry->name }}</strong><small>{{ $inquiry->email }}</small></td><td>{{ $inquiry->service?->title ?? 'Not selected' }}</td><td>{{ $inquiry->created_at->format('Y-m-d H:i') }}</td><td>{{ $inquiry->subject ?: '—' }}</td><td class="text-end"><a href="{{ route('admin.inquiries.show', $inquiry) }}">View message</a></td></tr>
@empty<tr><td colspan="5">No inquiries found.</td></tr>@endforelse
</tbody></table></div><div class="pagination-wrap">{{ $inquiries->links() }}</div>
@endsection
