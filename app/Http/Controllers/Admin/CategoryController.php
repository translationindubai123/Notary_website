<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $type = $this->type($request);
        $categories = ServiceCategory::where('service_type', $type)->with('parent')->withCount('services')
            ->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('admin.categories.index', compact('categories', 'type'));
    }

    public function create(Request $request)
    {
        $type = $this->type($request);

        return view('admin.categories.form', [
            'category' => new ServiceCategory(['service_type' => $type]),
            'parents' => ServiceCategory::where('service_type', $type)->orderBy('name')->get(),
            'type' => $type,
        ]);
    }

    public function store(Request $request)
    {
        $category = ServiceCategory::create($this->validated($request));

        return redirect()->route('admin.categories.edit', [$category, 'type' => $category->service_type])->with('status', 'Category created.');
    }

    public function edit(ServiceCategory $category)
    {
        return view('admin.categories.form', [
            'category' => $category,
            'parents' => ServiceCategory::where('service_type', $category->service_type)->where('id', '!=', $category->id)->orderBy('name')->get(),
            'type' => $category->service_type,
        ]);
    }

    public function update(Request $request, ServiceCategory $category)
    {
        $data = $this->validated($request, $category);
        $parentId = $data['parent_id'] ?? null;
        if ($parentId && ! ServiceCategory::whereKey($parentId)->where('service_type', $category->service_type)->exists()) {
            return back()->withErrors(['parent_id' => 'Parent category must belong to the same service section.'])->withInput();
        }
        $cursor = $parentId ? ServiceCategory::find($parentId) : null;
        $seen = [];

        while ($cursor) {
            if ($cursor->id === $category->id || isset($seen[$cursor->id])) {
                return back()->withErrors(['parent_id' => 'A category cannot be placed inside itself or one of its descendants.'])->withInput();
            }
            $seen[$cursor->id] = true;
            $cursor = $cursor->parent_id ? ServiceCategory::find($cursor->parent_id) : null;
        }

        DB::transaction(function () use ($category, $data) {
            $category->update($data);
        });

        return redirect()->route('admin.categories.edit', [$category, 'type' => $category->service_type])->with('status', 'Category updated.');
    }

    public function destroy(ServiceCategory $category)
    {
        if ($category->services()->exists() || $category->children()->exists()) {
            return back()->withErrors(['category' => 'This category has services or child categories. Reassign them before deleting it.']);
        }

        $category->delete();

        return redirect()->route('admin.categories.index', ['type' => $category->service_type])->with('status', 'Category deleted.');
    }

    private function validated(Request $request, ?ServiceCategory $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'slug' => [
                'required',
                'string',
                'max:190',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('service_categories', 'slug')->ignore($category),
                Rule::unique('slug_redirects', 'old_slug')->where('entity_type', 'category'),
            ],
            'service_type' => ['nullable', Rule::in(['notary', 'moj'])],
            'parent_id' => ['nullable', 'integer', 'exists:service_categories,id'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:30000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['service_type'] = $category ? $category->service_type : ($data['service_type'] ?? $request->query('type', 'notary'));
        if (! in_array($data['service_type'], ['notary', 'moj'], true)) {
            abort(422, 'Unknown service section.');
        }
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function type(Request $request): string
    {
        $type = $request->query('type', 'notary');
        abort_unless(in_array($type, ['notary', 'moj'], true), 404);

        return $type;
    }
}
