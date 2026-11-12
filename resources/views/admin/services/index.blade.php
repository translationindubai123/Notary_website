@extends('admin.layouts.app')
@section('title', 'Services')
@section('content')
<div class="admin-title"><div><span class="eyebrow">{{ $type === 'moj' ? 'MOJ content' : 'Notary content' }}</span><h1>{{ $type === 'moj' ? 'MOJ services' : 'Notary services' }}</h1></div><a class="button button-gold" href="{{ route('admin.services.create', ['type' => $type]) }}">Add service</a></div>
<div class="admin-actions mb-4"><a class="button {{ $type === 'notary' ? 'button-dark' : 'button-outline' }}" href="{{ route('admin.services.index', ['type' => 'notary']) }}">Notary services</a><a class="button {{ $type === 'moj' ? 'button-dark' : 'button-outline' }}" href="{{ route('admin.services.index', ['type' => 'moj']) }}">MOJ services</a></div>
<form class="admin-filter" method="get"><input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Search title or slug" aria-label="Search services"><select class="form-select" name="status" aria-label="Filter by publication"><option value="">All statuses</option><option value="published" @selected(request('status') === 'published')>Published</option><option value="draft" @selected(request('status') === 'draft')>Draft / scheduled</option></select><button class="button button-dark" type="submit">Filter</button></form>
<div class="admin-table-wrap"><table class="table admin-table"><thead><tr><th>Service</th><th>Category</th><th>Updated</th><th>Status</th><th></th></tr></thead><tbody>
@forelse ($services as $service)
<tr><td><strong>{{ $service->title }}</strong><small>/{{ $service->slug }}</small></td><td>{{ $service->category->name }}</td><td>{{ $service->updated_at->format('Y-m-d') }}</td><td>{{ $service->is_active && $service->published_at && $service->published_at->lte(now()) ? 'Published' : 'Draft' }}</td><td class="text-end"><a href="{{ route('admin.services.edit', [$service, 'type' => $type]) }}">Edit</a>
<form class="inline-form" method="post" action="{{ route('admin.services.destroy', [$service, 'type' => $type]) }}" onsubmit="return confirm('Delete this service?')">@csrf @method('delete')<button type="submit">Delete</button></form></td></tr>
@empty<tr><td colspan="5">No services yet.</td></tr>@endforelse
</tbody></table></div><div class="pagination-wrap">{{ $services->links() }}</div>
@endsection
