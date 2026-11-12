@extends('admin.layouts.app')
@section('title', $category->exists ? 'Edit category' : 'Create category')
@section('content')
<div class="admin-title"><div><span class="eyebrow">{{ $type === 'moj' ? 'MOJ content' : 'Notary content' }}</span><h1>{{ $category->exists ? 'Edit category' : 'Create category' }}</h1></div><a class="text-link" href="{{ route('admin.categories.index', ['type' => $type]) }}">← All categories</a></div>
<form class="admin-form" method="post" action="{{ $category->exists ? route('admin.categories.update', [$category, 'type' => $type]) : route('admin.categories.store', ['type' => $type]) }}">
    @csrf @if ($category->exists)@method('put')@endif
    <input type="hidden" name="service_type" value="{{ $type }}">
    <div class="admin-form-section"><h2>Category information</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="180"></div>
            <div class="col-md-6"><label class="form-label" for="slug">URL slug</label><input class="form-control" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" required maxlength="190" pattern="[a-z0-9]+(?:-[a-z0-9]+)*"><div class="form-text">Use lowercase letters, numbers, and hyphens. Previous public URLs are redirected if this changes.</div></div>
            <div class="col-md-6"><label class="form-label" for="parent_id">Parent category</label><select class="form-select" id="parent_id" name="parent_id"><option value="">Top-level category</option>@foreach ($parents as $parent)<option value="{{ $parent->id }}" @selected((string) old('parent_id', $category->parent_id) === (string) $parent->id)>{{ $parent->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label" for="sort_order">Sort order</label><input class="form-control" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}" required></div>
            <div class="col-12"><label class="form-label" for="short_description">Short description</label><textarea class="form-control" id="short_description" name="short_description" rows="2" maxlength="500">{{ old('short_description', $category->short_description) }}</textarea></div>
            <div class="col-12"><label class="form-label" for="description">Description</label><textarea class="form-control" id="description" name="description" rows="6">{{ old('description', $category->description) }}</textarea></div>
        </div>
    </div>
    <div class="admin-form-section"><h2>SEO</h2><div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="meta_title">Meta title</label><input class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}"></div>
        <div class="col-md-6"><label class="form-label" for="meta_description">Meta description</label><input class="form-control" id="meta_description" name="meta_description" value="{{ old('meta_description', $category->meta_description) }}" maxlength="320"></div>
        <div class="col-12"><label class="form-label" for="canonical_url">Canonical URL <span class="text-muted">(optional)</span></label><input class="form-control" id="canonical_url" type="url" name="canonical_url" value="{{ old('canonical_url', $category->canonical_url) }}"></div>
    </div></div>
    <label class="form-check mb-4"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))><span class="form-check-label">Active (visible on the public website)</span></label>
    <button class="button button-dark" type="submit">Save category</button>
</form>
@endsection
