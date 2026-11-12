@extends('admin.layouts.app')
@section('title', 'Categories')
@section('content')
<div class="admin-title"><div><span class="eyebrow">{{ $type === 'moj' ? 'MOJ content' : 'Notary content' }}</span><h1>{{ $type === 'moj' ? 'MOJ categories' : 'Notary service categories' }}</h1></div><a class="button button-gold" href="{{ route('admin.categories.create', ['type' => $type]) }}">Add category</a></div>
<div class="admin-actions mb-4"><a class="button {{ $type === 'notary' ? 'button-dark' : 'button-outline' }}" href="{{ route('admin.categories.index', ['type' => 'notary']) }}">Notary categories</a><a class="button {{ $type === 'moj' ? 'button-dark' : 'button-outline' }}" href="{{ route('admin.categories.index', ['type' => 'moj']) }}">MOJ categories</a></div>
<div class="admin-table-wrap"><table class="table admin-table"><thead><tr><th>Name</th><th>Parent</th><th>Services</th><th>Status</th><th></th></tr></thead><tbody>
@forelse ($categories as $category)
<tr><td><strong>{{ $category->name }}</strong><small>/{{ $category->slug }}</small></td><td>{{ $category->parent->name ?? '—' }}</td><td>{{ $category->services_count }}</td><td>{{ $category->is_active ? 'Active' : 'Hidden' }}</td><td class="text-end"><a href="{{ route('admin.categories.edit', [$category, 'type' => $type]) }}">Edit</a>
<form class="inline-form" method="post" action="{{ route('admin.categories.destroy', [$category, 'type' => $type]) }}" onsubmit="return confirm('Delete this category? It must not have services or child categories.')">@csrf @method('delete')<button type="submit">Delete</button></form></td></tr>
@empty<tr><td colspan="5">No categories yet.</td></tr>@endforelse
</tbody></table></div><div class="pagination-wrap">{{ $categories->links() }}</div>
@endsection
