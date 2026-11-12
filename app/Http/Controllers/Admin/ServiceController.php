<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $type = $this->type($request);
        $query = Service::whereHas('category', function ($builder) use ($type) {
            $builder->where('service_type', $type);
        })->with('category');

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', '%'.$search.'%')->orWhere('slug', 'like', '%'.$search.'%');
            });
        }
        if ($request->query('status') === 'published') {
            $query->published();
        } elseif ($request->query('status') === 'draft') {
            $query->where(function ($builder) {
                $builder->where('is_active', false)->orWhereNull('published_at')->orWhere('published_at', '>', now());
            });
        }

        $services = $query->orderByDesc('updated_at')->paginate(20)->withQueryString();

        return view('admin.services.index', compact('services', 'type'));
    }

    public function create(Request $request)
    {
        $type = $this->type($request);

        return view('admin.services.form', [
            'service' => new Service(),
            'categories' => ServiceCategory::where('service_type', $type)->orderBy('name')->get(),
            'otherServices' => Service::whereHas('category', function ($query) use ($type) {
                $query->where('service_type', $type);
            })->orderBy('title')->get(),
            'type' => $type,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['featured_image'] = $request->file('featured_image')?->store('services', 'public');
        $service = DB::transaction(function () use ($data, $request) {
            $service = Service::create($this->serviceFields($data, $request));
            $this->saveRelatedContent($service, $data);

            return $service;
        });

        return redirect()->route('admin.services.edit', [$service, 'type' => $service->category->service_type])->with('status', 'Service created.');
    }

    public function edit(Service $service)
    {
        $service->load(['documents', 'processSteps', 'faqs', 'relatedServices']);

        $type = $service->category->service_type;

        return view('admin.services.form', [
            'service' => $service,
            'categories' => ServiceCategory::where('service_type', $type)->orderBy('name')->get(),
            'otherServices' => Service::whereHas('category', function ($query) use ($type) {
                $query->where('service_type', $type);
            })->whereKeyNot($service->id)->orderBy('title')->get(),
            'type' => $type,
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validated($request, $service);
        $oldImage = $service->featured_image;
        $newImage = null;
        if ($request->hasFile('featured_image')) {
            $newImage = $request->file('featured_image')->store('services', 'public');
            $data['featured_image'] = $newImage;
        }
        DB::transaction(function () use ($data, $request, $service) {
            $service->update($this->serviceFields($data, $request));
            $this->saveRelatedContent($service, $data);
        });
        if ($newImage && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.services.edit', [$service, 'type' => $service->category->service_type])->with('status', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $image = $service->featured_image;
        DB::transaction(function () use ($service) {
            $service->relatedServices()->detach();
            $service->delete();
        });
        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return redirect()->route('admin.services.index', ['type' => $service->category->service_type])->with('status', 'Service deleted.');
    }

    private function validated(Request $request, ?Service $service = null): array
    {
        $type = $service
            ? $service->category->service_type
            : $request->input('service_type', $request->query('type', 'notary'));
        if (! in_array($type, ['notary', 'moj'], true)) {
            abort(422, 'Unknown service section.');
        }

        $data = $request->validate([
            'service_category_id' => ['required', 'integer', 'exists:service_categories,id'],
            'title' => ['required', 'string', 'max:190'],
            'slug' => [
                'required',
                'string',
                'max:190',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('services', 'slug')->ignore($service),
                Rule::unique('slug_redirects', 'old_slug')->where('entity_type', 'service'),
            ],
            'short_description' => ['nullable', 'string', 'max:500'],
            'introduction' => ['nullable', 'string', 'max:30000'],
            'description' => ['nullable', 'string', 'max:100000'],
            'eligibility' => ['nullable', 'string', 'max:30000'],
            'processing_time' => ['nullable', 'string', 'max:255'],
            'service_availability' => ['nullable', 'string', 'max:255'],
            'important_notes' => ['nullable', 'string', 'max:30000'],
            'who_needs_service' => ['nullable', 'string', 'max:30000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:320'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_alt' => ['nullable', 'string', 'max:255', 'required_with:featured_image'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'documents' => ['nullable', 'array', 'max:50'],
            'documents.*.document_name' => ['nullable', 'string', 'max:255'],
            'documents.*.document_description' => ['nullable', 'string', 'max:2000'],
            'process_steps' => ['nullable', 'array', 'max:50'],
            'process_steps.*.step_title' => ['nullable', 'string', 'max:255', 'required_with:process_steps.*.step_description'],
            'process_steps.*.step_description' => ['nullable', 'string', 'max:4000', 'required_with:process_steps.*.step_title'],
            'faqs' => ['nullable', 'array', 'max:50'],
            'faqs.*.question' => ['nullable', 'string', 'max:500', 'required_with:faqs.*.answer'],
            'faqs.*.answer' => ['nullable', 'string', 'max:4000', 'required_with:faqs.*.question'],
            'related_services' => ['nullable', 'array', 'max:50'],
            'related_services.*' => ['integer', 'distinct', Rule::exists('services', 'id')->where(function ($query) use ($service, $type) {
                if ($service) {
                    $query->where('id', '!=', $service->id);
                }
                $query->whereIn('service_category_id', ServiceCategory::where('service_type', $type)->select('id'));
            })],
        ]);
        if (! ServiceCategory::whereKey($data['service_category_id'])->where('service_type', $type)->exists()) {
            abort(422, 'Choose a category from the same service section.');
        }

        return $data;
    }

    private function serviceFields(array $data, Request $request): array
    {
        unset($data['documents'], $data['process_steps'], $data['faqs'], $data['related_services']);
        if (! is_string($data['featured_image'] ?? null)) {
            unset($data['featured_image']);
        }
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        if ($data['is_active'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        if (! $data['is_active']) {
            $data['published_at'] = $data['published_at'] ?? null;
        }

        return $data;
    }

    private function saveRelatedContent(Service $service, array $data): void
    {
        $service->documents()->delete();
        foreach ($data['documents'] ?? [] as $order => $document) {
            if (! empty($document['document_name'])) {
                $service->documents()->create([
                    'document_name' => $document['document_name'],
                    'document_description' => $document['document_description'] ?? null,
                    'sort_order' => $order,
                ]);
            }
        }

        $service->processSteps()->delete();
        foreach ($data['process_steps'] ?? [] as $order => $step) {
            if (! empty($step['step_title'])) {
                $service->processSteps()->create([
                    'step_title' => $step['step_title'],
                    'step_description' => $step['step_description'],
                    'sort_order' => $order,
                ]);
            }
        }

        $service->faqs()->delete();
        foreach ($data['faqs'] ?? [] as $order => $faq) {
            if (! empty($faq['question'])) {
                $service->faqs()->create([
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                    'sort_order' => $order,
                ]);
            }
        }

        $service->relatedServices()->sync($data['related_services'] ?? []);
    }

    private function type(Request $request): string
    {
        $type = $request->query('type', 'notary');
        abort_unless(in_array($type, ['notary', 'moj'], true), 404);

        return $type;
    }
}
