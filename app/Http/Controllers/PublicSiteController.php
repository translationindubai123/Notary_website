<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceFaq;
use App\Models\SlugRedirect;
use Illuminate\Http\Request;

class PublicSiteController extends Controller
{
    public function home()
    {
        $categories = ServiceCategory::active()
            ->whereNull('parent_id')
            ->where('service_type', 'notary')
            ->withCount(['services' => function ($query) {
                $query->published();
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $featuredServices = Service::published()
            ->whereHas('category', function ($query) {
                $query->where('service_type', 'notary');
            })
            ->where('is_featured', true)
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->take(6)
            ->get();
        $services = Service::published()->orderBy('title')->get(['id', 'title']);
        $selectedServiceId = old('service_id');
        $homepageFaqs = [
            [
                'question' => 'What notary services are available in Dubai?',
                'answer' => 'Notary services in Dubai include the notarisation of Power of Attorney documents, declarations, acknowledgments, agreements, company documents, legal notices and other eligible legal instruments. The required procedure depends on the document type, the parties involved and the relevant authority. Contact our team to identify the appropriate service for your requirements.',
            ],
            [
                'question' => 'How can I get a document notarised in Dubai?',
                'answer' => 'To get a document notarised in Dubai, first identify the type of document and the purpose for which it will be used. Prepare the required identification and supporting documents, submit your application through the appropriate channel, and complete the applicable verification, signing and fee-payment steps. Requirements vary by document and notary service.',
            ],
            [
                'question' => 'How can I get a Power of Attorney (POA) in Dubai?',
                'answer' => 'You can request a Power of Attorney in Dubai for eligible personal, property, business or other legal matters. The POA must clearly specify the authority being granted and comply with the applicable requirements. The required documents and notarisation process depend on the type of POA and the parties involved.',
            ],
            [
                'question' => 'What documents are required for notarisation in Dubai?',
                'answer' => 'Common requirements may include a valid Emirates ID or passport, the document to be notarised, relevant supporting records and, where applicable, a valid authorisation for a representative. Company documents and documents issued outside the UAE may require additional evidence, translation or authentication. Confirm the exact requirements before submitting your application.',
            ],
            [
                'question' => 'How much do notary services cost in Dubai?',
                'answer' => 'The cost of notary services in Dubai depends on the document type, number of signatures or pages, applicable government fees and any additional services required. Private service-provider fees may also differ. Request a quotation based on your specific document before proceeding.',
            ],
            [
                'question' => 'Can I get documents notarised online in Dubai?',
                'answer' => 'Certain notary services in Dubai may be available through approved digital channels, subject to eligibility, identity verification and the requirements of the relevant authority. Other documents may require an in-person appointment or additional steps. Confirm whether your specific document qualifies for online notarisation before applying.',
            ],
            [
                'question' => 'What is the difference between notarisation, attestation and legal translation?',
                'answer' => 'Notarisation involves an authorised notary performing a legally recognised act, such as witnessing a signature or authenticating an instrument. Attestation verifies a signature, seal or document through the relevant authority, depending on the procedure. Legal translation converts a document into another language through an appropriately qualified or authorised translator. Some documents require more than one of these services.',
            ],
            [
                'question' => 'How can I request a notary service in Dubai?',
                'answer' => 'To request a notary service in Dubai, share the type of document you need processed, its intended purpose and any relevant deadline through our contact form or WhatsApp. Our team can help identify the likely procedure, explain the next steps and advise which supporting documents may be required. Final acceptance and processing remain subject to the relevant authority.',
            ],
        ];
        $homepageFaqData = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function ($faq) {
                return [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
                ];
            }, $homepageFaqs),
        ];

        return view('pages.home', compact('categories', 'featuredServices', 'services', 'selectedServiceId', 'homepageFaqs', 'homepageFaqData'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function services(Request $request)
    {
        return $this->servicesByType($request, 'notary');
    }

    public function mojServices(Request $request)
    {
        return $this->servicesByType($request, 'moj');
    }

    private function servicesByType(Request $request, string $type)
    {
        $query = Service::published()->whereHas('category', function ($builder) use ($type) {
            $builder->where('service_type', $type);
        })->with('category');

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', '%'.$search.'%')
                    ->orWhere('short_description', 'like', '%'.$search.'%')
                    ->orWhere('introduction', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($builder) use ($request) {
                $builder->where('slug', $request->query('category'))
                    ->where('is_active', true)
                    ->where('service_type', $request->routeIs('moj.services.*') ? 'moj' : 'notary');
            });
        }

        $services = $query->orderBy('sort_order')->orderBy('title')->paginate(12)->withQueryString();
        $categories = ServiceCategory::active()->where('service_type', $type)->orderBy('sort_order')->orderBy('name')->get();
        $serviceType = $type;

        return view('pages.services', compact('services', 'categories', 'serviceType'));
    }

    public function category(string $category)
    {
        return $this->showCategory($category, 'notary');
    }

    public function mojCategory(string $category)
    {
        return $this->showCategory($category, 'moj');
    }

    public function legacyCategory(string $category)
    {
        return $this->redirectLegacyCategory($category, 'notary');
    }

    public function legacyMojCategory(string $category)
    {
        return $this->redirectLegacyCategory($category, 'moj');
    }

    private function redirectLegacyCategory(string $slug, string $type)
    {
        $category = ServiceCategory::where('slug', $slug)->first();
        if (! $category) {
            $targetId = SlugRedirect::targetId('category', $slug);
            $category = $targetId ? ServiceCategory::find($targetId) : null;
        }

        abort_unless($category && $category->is_active && $category->service_type === $type, 404);

        return redirect()->to($category->publicCategoryUrl(), 301);
    }

    private function showCategory(string $slug, string $type)
    {
        $category = ServiceCategory::where('slug', $slug)->first();
        if (! $category) {
            $targetId = SlugRedirect::targetId('category', $slug);
            $category = $targetId ? ServiceCategory::find($targetId) : null;
            if (! $category) {
                abort(404);
            }

            return redirect()->route($category->service_type === 'moj' ? 'moj.services.category' : 'services.category', $category->slug, 301);
        }

        abort_unless($category->is_active && $category->service_type === $type, 404);

        $services = $category->services()
            ->published()
            ->with('category')
            ->paginate(12);
        $children = $category->children()->active()->get();
        $breadcrumbs = $this->categoryBreadcrumbs($category);
        $serviceType = $type;

        return view('pages.category', compact('category', 'services', 'children', 'breadcrumbs', 'serviceType'));
    }

    public function service(string $category, string $service)
    {
        return $this->showService($category, $service, 'notary');
    }

    public function mojService(string $category, string $service)
    {
        return $this->showService($category, $service, 'moj');
    }

    public function legacyService(string $service)
    {
        return $this->redirectLegacyService($service, 'notary');
    }

    public function legacyMojService(string $service)
    {
        return $this->redirectLegacyService($service, 'moj');
    }

    private function redirectLegacyService(string $slug, string $type)
    {
        $service = Service::where('slug', $slug)->first();
        if (! $service) {
            $targetId = SlugRedirect::targetId('service', $slug);
            $service = $targetId ? Service::find($targetId) : null;
        }

        abort_unless($service && $service->is_active && $service->published_at && $service->published_at->lte(now()), 404);
        abort_unless($service->category()->where('is_active', true)->where('service_type', $type)->exists(), 404);

        return redirect()->to($service->publicUrl(), 301);
    }

    private function showService(string $categorySlug, string $slug, string $type)
    {
        $service = Service::where('slug', $slug)->first();
        if (! $service) {
            $targetId = SlugRedirect::targetId('service', $slug);
            $service = $targetId ? Service::find($targetId) : null;
        }

        abort_unless($service && $service->is_active && $service->published_at && $service->published_at->lte(now()), 404);
        abort_unless($service->category()->where('is_active', true)->where('service_type', $type)->exists(), 404);

        if ($service->category->slug !== $categorySlug) {
            $categoryTargetId = SlugRedirect::targetId('category', $categorySlug);
            abort_unless($categoryTargetId === $service->category->id, 404);

            return redirect()->to($service->publicUrl(), 301);
        }

        if ($service->slug !== $slug) {
            return redirect()->to($service->publicUrl(), 301);
        }

        return $this->servicePage($service, false, $type);
    }

    public function preview(Service $service)
    {
        return $this->servicePage($service, true, $service->category->service_type);
    }

    private function servicePage(Service $service, bool $isPreview, string $type)
    {
        $service->load([
            'category.parent',
            'documents',
            'processSteps',
            'faqs',
            'relatedServices' => function ($query) use ($type) {
                $query->published()
                    ->whereHas('category', function ($categoryQuery) use ($type) {
                        $categoryQuery->where('service_type', $type);
                    })
                    ->with('category')
                    ->orderBy('title')
                    ->take(4);
            },
        ]);
        $isPublished = $service->is_active
            && $service->published_at
            && $service->published_at->lte(now())
            && $service->category->is_active;
        $breadcrumbs = $this->categoryBreadcrumbs($service->category);
        $breadcrumbs[] = ['label' => $service->title];

        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->title,
            'description' => $service->meta_description ?: $service->short_description,
            'provider' => ['@id' => rtrim(config('app.url'), '/').'#business'],
            'url' => $service->canonical_url ?: $service->publicUrl(),
        ];
        $faqData = null;
        if ($service->faqs->isNotEmpty()) {
            $faqData = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $service->faqs->map(function ($faq) {
                    return [
                        '@type' => 'Question',
                        'name' => $faq->question,
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->answer],
                    ];
                })->values()->all(),
            ];
        }

        $serviceType = $type;

        return view('pages.service', compact('service', 'structuredData', 'faqData', 'isPreview', 'isPublished', 'breadcrumbs', 'serviceType'));
    }

    private function categoryBreadcrumbs(ServiceCategory $category): array
    {
        $ancestors = collect();
        $current = $category;

        while ($current) {
            $ancestors->prepend($current);
            $current = $current->parent;
        }

        $isMoj = $category->service_type === 'moj';
        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => $isMoj ? 'MOJ Services' : 'Notary Services', 'url' => route($isMoj ? 'moj' : 'services.index')],
        ];

        foreach ($ancestors as $ancestor) {
            $breadcrumbs[] = [
                'label' => $ancestor->name,
                'url' => $ancestor->publicCategoryUrl(),
            ];
        }

        return $breadcrumbs;
    }

    public function moj()
    {
        $categories = ServiceCategory::active()
            ->where('service_type', 'moj')
            ->whereNull('parent_id')
            ->withCount(['services' => function ($query) {
                $query->published();
            }])
            ->orderBy('sort_order')->orderBy('name')->get();
        $searchCategories = ServiceCategory::active()
            ->where('service_type', 'moj')
            ->orderBy('sort_order')->orderBy('name')->get();
        $relatedServices = Service::published()
            ->whereHas('category', function ($query) {
                $query->where('service_type', 'moj');
            })
            ->with('category')
            ->orderBy('is_featured', 'desc')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->take(3)
            ->get();

        return view('pages.moj', compact('categories', 'searchCategories', 'relatedServices'));
    }

    public function robots()
    {
        $content = "User-agent: *\nDisallow: /admin/\nDisallow: /admin\nSitemap: ".route('sitemap')."\n";

        return response($content)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap()
    {
        $categories = ServiceCategory::active()->orderBy('updated_at')->get();
        $services = Service::published()->with('category')->orderBy('updated_at')->get();
        $staticPages = [
            ['url' => route('home')],
            ['url' => rtrim(route('about'), '/').'/'],
            ['url' => route('services.index')],
            ['url' => route('moj')],
            ['url' => route('moj.services.index')],
            ['url' => route('contact')],
        ];

        return response()
            ->view('sitemap', compact('categories', 'services', 'staticPages'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
