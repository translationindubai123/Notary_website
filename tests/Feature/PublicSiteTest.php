<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_nap_is_consistent_across_public_pages(): void
    {
        $nap = [
            'Al Syed Documents Clearing Services',
            '+971 55 625 1709',
            'Almas Tower - Dubai Lake Avenue - Street 66 - Dubai - United Arab Emirates',
        ];

        foreach (['/', '/about-us/', '/notary-services', '/moj-services', '/contact'] as $path) {
            $response = $this->get($path)->assertOk();

            foreach ($nap as $value) {
                $response->assertSee($value);
            }

            $response->assertSee('#business')
                ->assertSee('tel:+971556251709', false)
                ->assertSee('https://wa.me/971556251709', false);
            if ($path === '/contact') {
                $response->assertSee('contact-map-frame', false)
                    ->assertSee(rawurlencode($nap[2]), false)
                    ->assertSee('"@type":"ContactPage"', false);
            }
        }
    }

    public function test_public_pages_render_and_service_content_is_database_driven(): void
    {
        $category = ServiceCategory::create(['name' => 'Smart Document Request', 'slug' => 'smart-document-request', 'is_active' => true]);
        $service = Service::create([
            'service_category_id' => $category->id,
            'title' => 'Power of Attorney',
            'slug' => 'power-of-attorney',
            'short_description' => 'Service introduction.',
            'description' => 'A detailed service overview.',
            'eligibility' => 'Eligibility information.',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);
        $service->documents()->create(['document_name' => 'Valid identification']);
        $service->processSteps()->create(['step_title' => 'Prepare documents', 'step_description' => 'Gather the required documents.']);
        $this->get('/')->assertOk()->assertSee('Smart Document Request');
        $this->get('/about')->assertRedirect('/about-us/');
        $this->get('/about-us/')
            ->assertOk()
            ->assertSee('<title>About Us | Al Syed Documents Clearing Services Dubai</title>', false)
            ->assertSee('<meta name="description" content="Learn about Al Syed Documents Clearing Services in Dubai. Discover our commitment to professional document clearing, notary assistance, and reliable documentation support."', false)
            ->assertSee('<link rel="canonical" href="https://dubainotaryservice.ae/about-us/">', false)
            ->assertSee('Making Every Document Journey Simpler')
            ->assertSee('The Beginning — A Clear Purpose')
            ->assertSee('Documentation Support for Different Needs')
            ->assertSee('Commitment to Confidentiality')
            ->assertSee('Our mission')
            ->assertSee('Our vision')
            ->assertSee('href="tel:+971556251709"', false)
            ->assertSee('href="https://dubainotaryservice.ae/about-us/"', false);
        $this->get('/notary-services')->assertOk()->assertSee('Power of Attorney');
        $this->get('/notary-services/smart-document-request')->assertOk()->assertSee('Power of Attorney');
        $this->get($service->publicUrl())
            ->assertOk()
            ->assertSee('Power of Attorney')
            ->assertSee('A detailed service overview.')
            ->assertSee('Valid identification')
            ->assertSee('Prepare documents')
            ->assertSee('aria-label="On this page"', false)
            ->assertSee('href="#required-documents"', false)
            ->assertSee('href="#process-steps"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee($service->publicUrl(), false);
        $this->get('/moj-services')->assertOk();
        $this->get('/contact')->assertOk();
    }

    public function test_moj_landing_page_uses_requested_seo_content_and_service_directory_design(): void
    {
        $category = ServiceCategory::create([
            'name' => 'Legal Translation',
            'slug' => 'legal-translation',
            'service_type' => 'moj',
            'is_active' => true,
        ]);
        Service::create([
            'service_category_id' => $category->id,
            'title' => 'MOJ Certified Translation',
            'slug' => 'moj-certified-translation',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->get('/moj-services');
        $response
            ->assertOk()
            ->assertSee('<title>MOJ Services in Dubai | Legal Translation &amp; Documents</title>', false)
            ->assertSee('name="description" content="Explore MOJ-related services in Dubai, including MOJ-certified legal translation, document authentication, attestation guidance and eligible legal document procedures."', false)
            ->assertSee('What service or document are you looking for?')
            ->assertSee('name="q"', false)
            ->assertSee('name="category"', false)
            ->assertSee('MOJ Services by Category')
            ->assertSee('Official deed authentication')
            ->assertSee('Attestation guidance')
            ->assertSee(route('moj.services.index', ['q' => 'legal translation']), false)
            ->assertSee('What Are MOJ Services?')
            ->assertSee('How to Find the Right MOJ Service')
            ->assertSee('Translation, Authentication and Attestation')
            ->assertSee('MOJ Certified Translation')
            ->assertSee('moj-directory-hero', false)
            ->assertSee('does not imply government affiliation or endorsement')
            ->assertSee('Frequently Asked Questions About MOJ Services in Dubai')
            ->assertSee('MOJ attestation and MOFA attestation')
            ->assertSee('application/ld+json', false);
        $this->assertSame(12, substr_count($response->getContent(), '<details>'));
        $this->get('/moj-services/services?q=MOJ+Certified')
            ->assertOk()
            ->assertSee('MOJ Certified Translation');
    }

    public function test_notary_directory_uses_requested_seo_content_and_keeps_searchable_catalogue(): void
    {
        $category = ServiceCategory::create(['name' => 'Power of Attorney Services', 'slug' => 'power-of-attorney-services', 'is_active' => true]);
        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Special Power of Attorney',
            'slug' => 'special-power-of-attorney',
            'short_description' => 'Document-specific notary information.',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->get('/notary-services');
        $response->assertOk()
            ->assertSee('<title>Notary Services in Dubai | Document Notarisation</title>', false)
            ->assertSee('name="description" content="Find notary services in Dubai for Power of Attorney, declarations, company documents, legal notices and other eligible legal instruments. Explore service types and document requirements."', false)
            ->assertSee('<link rel="canonical" href="'.route('services.index').'">', false)
            ->assertSee('Notary Services in Dubai')
            ->assertSee('Explore Our Notary Services and Document Requirements')
            ->assertSee('Power of Attorney, a declaration, a company agreement, a legal notice', false)
            ->assertSee('id="notary-service-search"', false)
            ->assertSee('id="notary-service-categories"', false)
            ->assertSee('Special Power of Attorney')
            ->assertSee('company document', false)
            ->assertSee('Documents Commonly Required for Notary Services')
            ->assertSee('Notarisation, Attestation and Legal Translation')
            ->assertSee('Notary Service Fees and Processing Time')
            ->assertSee('Frequently Asked Questions About Notary Services in Dubai')
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('name="robots" content="index,follow"', false);

        $this->get('/notary-services?q=power')
            ->assertOk()
            ->assertSee('Special Power of Attorney')
            ->assertSee('name="robots" content="noindex,follow"', false);
    }

    public function test_notary_directory_shortcut_boxes_show_only_top_level_categories(): void
    {
        $parent = ServiceCategory::create([
            'name' => 'Personal Documents',
            'slug' => 'personal-documents',
            'is_active' => true,
        ]);
        ServiceCategory::create([
            'parent_id' => $parent->id,
            'name' => 'Power of Attorney',
            'slug' => 'power-of-attorney-category',
            'is_active' => true,
        ]);
        $otherParent = ServiceCategory::create([
            'name' => 'Business Documents',
            'slug' => 'business-documents',
            'is_active' => true,
        ]);
        ServiceCategory::create([
            'parent_id' => $otherParent->id,
            'name' => 'Company Agreements',
            'slug' => 'company-agreements-category',
            'is_active' => true,
        ]);
        ServiceCategory::create([
            'name' => 'MOJ-only category',
            'slug' => 'moj-only-category',
            'service_type' => 'moj',
            'is_active' => true,
        ]);
        Service::create([
            'service_category_id' => $parent->id,
            'title' => 'Special Power of Attorney Service',
            'slug' => 'special-power-of-attorney-service',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->get('/notary-services');
        $response->assertOk()
            ->assertSee('Personal Documents')
            ->assertSee('Business Documents')
            ->assertSee('Power of Attorney')
            ->assertSee('Special Power of Attorney Service');

        preg_match('/<nav class="category-shortcuts notary-category-shortcuts".*?<\/nav>/s', $response->getContent(), $shortcuts);
        $this->assertNotEmpty($shortcuts);
        $this->assertStringContainsString('Personal Documents', $shortcuts[0]);
        $this->assertStringContainsString('Business Documents', $shortcuts[0]);
        $this->assertStringNotContainsString('Power of Attorney</a>', $shortcuts[0]);
        $this->assertStringNotContainsString('Company Agreements', $shortcuts[0]);
        $this->assertStringNotContainsString('MOJ-only category', $shortcuts[0]);
        $this->assertStringNotContainsString('Special Power of Attorney Service', $shortcuts[0]);
    }

    public function test_category_subcategories_use_a_responsive_grid_and_wrap_long_descriptions(): void
    {
        $parent = ServiceCategory::create([
            'name' => 'Parent category',
            'slug' => 'parent-category',
            'short_description' => str_repeat('longdescription', 50),
            'description' => str_repeat('longdescription', 80),
            'is_active' => true,
        ]);
        foreach (range(1, 3) as $number) {
            ServiceCategory::create([
                'parent_id' => $parent->id,
                'name' => 'Subcategory '.$number,
                'slug' => 'subcategory-'.$number,
                'short_description' => str_repeat('longtext', 40),
                'is_active' => true,
            ]);
        }

        $this->get('/notary-services/parent-category')
            ->assertOk()
            ->assertSee('category-grid category-grid-subcategories', false)
            ->assertSee('Subcategory 1')
            ->assertSee('Subcategory 2')
            ->assertSee('Subcategory 3');
    }

    public function test_homepage_uses_configured_business_name_and_requested_seo_and_hero_copy(): void
    {
        config(['site.name' => 'Example Notary Business']);

        $response = $this->get('/');
        $response->assertOk()
            ->assertSee('<title>Notary Services in Dubai | Example Notary Business</title>', false)
            ->assertSee('name="description" content="Looking for notary services in Dubai? Explore document notarisation, powers of attorney, company documents, legal notices and other notary services. Contact our team for guidance."', false)
            ->assertSee('PROFESSIONAL DOCUMENT SERVICES · DUBAI, UAE')
            ->assertSee('Notary Services in Dubai for Personal and Business Documents')
            ->assertSee('Find the right notary service for your document.')
            ->assertSee('Service requirements depend on the document and the relevant authority.')
            ->assertSee('Explore Our Services')
            ->assertSee('Contact Our Team')
            ->assertSee('href="tel:+971556251709"', false);

        $this->assertSame(1, substr_count($response->getContent(), '<h1'));
    }

    public function test_selected_homepage_sections_have_scroll_reveal_markers(): void
    {
        $category = ServiceCategory::create([
            'name' => 'Requests',
            'slug' => 'requests',
            'is_active' => true,
        ]);
        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Featured service',
            'slug' => 'featured-service',
            'is_active' => true,
            'is_featured' => true,
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->get('/');
        $response->assertOk()
            ->assertSee('class="container" data-scroll-reveal', false)
            ->assertSee('class="container process-layout" data-scroll-reveal', false);

        $this->assertSame(5, substr_count($response->getContent(), 'data-scroll-reveal'));
    }

    public function test_homepage_hero_uses_the_shared_contact_form_with_published_service_options(): void
    {
        $category = ServiceCategory::create(['name' => 'Requests', 'slug' => 'requests', 'is_active' => true]);
        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Published service',
            'slug' => 'published-service',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);
        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Draft service',
            'slug' => 'draft-service',
            'is_active' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('class="contact-form home-contact-form"', false)
            ->assertSee('action="'.route('contact.store').'"', false)
            ->assertSee('Tell us how we can help')
            ->assertSee('Published service')
            ->assertDontSee('Draft service')
            ->assertSee('name="company_website"', false);

        $this->get('/contact')
            ->assertOk()
            ->assertSee('class="contact-form "', false)
            ->assertSee('Please do not include passport numbers, Emirates IDs, or confidential documents in this form.');
    }

    public function test_authority_and_certification_logos_are_not_displayed_on_public_directories(): void
    {
        foreach (['/', '/notary-services', '/moj-services'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertDontSee('images/trust-signals/')
                ->assertDontSee('home-trust-logos');
        }
    }

    public function test_homepage_displays_country_flags_in_international_documents_section(): void
    {
        $response = $this->get('/');
        $response->assertOk()
            ->assertSee('Documents Connected to Different Countries')
            ->assertSee('alt="Flag of Canada"', false)
            ->assertSee('alt="Flag of Pakistan"', false)
            ->assertSee('alt="Flag of the United Kingdom"', false)
            ->assertSee('alt="Flag of the United States"', false)
            ->assertSee('alt="Flag of China"', false)
            ->assertSee('alt="Flag of the Philippines"', false)
            ->assertSee('>Canada</span>', false)
            ->assertSee('>Pakistan</span>', false)
            ->assertSee('>United Kingdom</span>', false)
            ->assertSee('>United States</span>', false)
            ->assertSee('>China</span>', false)
            ->assertSee('>Philippines</span>', false);

        $content = $response->getContent();
        $internationalPosition = strpos($content, 'Documents Connected to Different Countries');
        $flagsPosition = strpos($content, 'class="home-country-flags"');
        $serviceExperiencePosition = strpos($content, 'Clear Information. A More Straightforward Service Experience.');
        $this->assertNotFalse($internationalPosition);
        $this->assertNotFalse($flagsPosition);
        $this->assertNotFalse($serviceExperiencePosition);
        $this->assertTrue($internationalPosition < $flagsPosition);
        $this->assertTrue($serviceExperiencePosition < $internationalPosition);
        $this->assertSame(6, substr_count($content, 'images/flags/'));
    }

    public function test_homepage_shows_its_own_faqs_independent_of_service_faqs(): void
    {
        $category = ServiceCategory::create(['name' => 'Requests', 'slug' => 'requests', 'is_active' => true]);
        $published = Service::create([
            'service_category_id' => $category->id,
            'title' => 'Published service',
            'slug' => 'published-service',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);
        $draft = Service::create([
            'service_category_id' => $category->id,
            'title' => 'Draft service',
            'slug' => 'draft-service',
            'is_active' => false,
        ]);
        $published->faqs()->create(['question' => 'Published question?', 'answer' => 'Published answer.']);
        $draft->faqs()->create(['question' => 'Draft question?', 'answer' => 'Draft answer.']);

        $response = $this->get('/');
        $response->assertOk()
            ->assertSee('Power of Attorney documents, declarations, acknowledgments, agreements, company documents, legal notices', false)
            ->assertDontSee('Published question?')
            ->assertDontSee('Draft question?')
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('"@type":"Question"', false);

        foreach ([
            'What notary services are available in Dubai?',
            'How can I get a document notarised in Dubai?',
            'How can I get a Power of Attorney (POA) in Dubai?',
            'What documents are required for notarisation in Dubai?',
            'How much do notary services cost in Dubai?',
            'Can I get documents notarised online in Dubai?',
            'What is the difference between notarisation, attestation and legal translation?',
            'How can I request a notary service in Dubai?',
        ] as $question) {
            $response->assertSee($question);
        }

        $this->assertSame(8, substr_count($response->getContent(), '"@type":"Question"'));
    }

    public function test_public_layout_has_responsive_navigation_and_accessible_contact_actions(): void
    {
        config(['site.phone' => '+971501234567', 'site.whatsapp' => '+971501234567']);

        $this->get('/')->assertOk()
            ->assertSee('data-nav-toggle', false)
            ->assertSee('aria-controls="main-navigation"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('https://wa.me/971501234567', false);
    }

    public function test_navigation_shows_published_services_limited_to_five_per_category(): void
    {
        $category = ServiceCategory::create([
            'name' => 'Notification by Publication',
            'slug' => 'notification-by-publication',
            'is_active' => true,
        ]);

        foreach (range(1, 6) as $number) {
            Service::create([
                'service_category_id' => $category->id,
                'title' => 'Publication service '.$number,
                'slug' => 'publication-service-'.$number,
                'is_active' => true,
                'published_at' => now()->subMinute(),
                'sort_order' => $number,
            ]);
        }

        $response = $this->get('/');
        $response->assertOk()
            ->assertSee('Notification by Publication')
            ->assertSee('Publication service 1')
            ->assertSee('Publication service 5')
            ->assertSee('View all Notification by Publication services')
            ->assertSee(route('services.show', ['category' => $category->slug, 'service' => 'publication-service-5']), false)
            ->assertSee(route('services.category', $category->slug), false);

        preg_match('/<nav class="main-nav".*?<\/nav>/s', $response->getContent(), $navigation);
        $this->assertNotEmpty($navigation);
        $this->assertStringNotContainsString('Publication service 6', $navigation[0]);
    }

    public function test_navigation_category_without_published_services_still_has_view_all_submenu(): void
    {
        $category = ServiceCategory::create([
            'name' => 'Notification by Publication',
            'slug' => 'notification-by-publication',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Show Notification by Publication services')
            ->assertSee('No published services yet.')
            ->assertSee('View all Notification by Publication services')
            ->assertSee(route('services.category', $category->slug), false);
    }

    public function test_moj_services_have_separate_categories_public_pages_and_navigation(): void
    {
        $notaryCategory = ServiceCategory::create([
            'name' => 'Notary category',
            'slug' => 'notary-category',
            'is_active' => true,
        ]);
        $mojCategory = ServiceCategory::create([
            'name' => 'MOJ category',
            'slug' => 'moj-category',
            'service_type' => 'moj',
            'is_active' => true,
        ]);
        Service::create([
            'service_category_id' => $notaryCategory->id,
            'title' => 'Notary-only service',
            'slug' => 'notary-only-service',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);
        $mojService = Service::create([
            'service_category_id' => $mojCategory->id,
            'title' => 'MOJ document service',
            'slug' => 'moj-document-service',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $mojLanding = $this->get('/moj-services');
        $mojLanding
            ->assertOk()
            ->assertSee('MOJ category')
            ->assertSee('MOJ document service');
        $this->assertSame(1, substr_count($mojLanding->getContent(), 'class="service-card"'));
        $mojListing = $this->get('/moj-services/services');
        $mojListing
            ->assertOk()
            ->assertSee('MOJ document service');
        $this->assertSame(1, substr_count($mojListing->getContent(), 'class="service-card"'));
        $this->get('/moj-services/moj-category')
            ->assertOk()
            ->assertSee('MOJ document service');
        $this->get('/moj-services/moj-category/moj-document-service')
            ->assertOk()
            ->assertSee('MOJ document service')
            ->assertSee($mojService->publicUrl(), false);
        $notaryListing = $this->get('/notary-services');
        $notaryListing
            ->assertOk()
            ->assertSee('Notary-only service');
        $this->assertSame(1, substr_count($notaryListing->getContent(), 'class="service-card"'));
        $this->get('/notary-services/category/moj-category')->assertNotFound();
        $this->get('/notary-services/service/moj-document-service')->assertNotFound();
        $this->get('/')
            ->assertOk()
            ->assertSee('moj-category-navigation', false)
            ->assertSee(route('moj.services.category', $mojCategory->slug), false)
            ->assertSee($mojService->publicUrl(), false);
    }

    public function test_unpublished_and_unknown_services_are_not_public(): void
    {
        $category = ServiceCategory::create(['name' => 'Requests', 'slug' => 'requests', 'is_active' => true]);
        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Draft item',
            'slug' => 'draft-item',
            'is_active' => false,
        ]);

        $this->get('/notary-services/requests/draft-item')->assertNotFound();
        $this->get('/notary-services/requests/not-here')->assertNotFound();
    }

    public function test_deactivating_a_category_hides_its_published_services(): void
    {
        $category = ServiceCategory::create(['name' => 'Requests', 'slug' => 'requests', 'is_active' => false]);
        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Hidden category service',
            'slug' => 'hidden-category-service',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $this->get('/notary-services')->assertDontSee('Hidden category service');
        $this->get('/notary-services/requests/hidden-category-service')->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee('/notary-services/requests/hidden-category-service', false);
    }

    public function test_sitemap_contains_only_public_service_urls(): void
    {
        $category = ServiceCategory::create(['name' => 'Requests', 'slug' => 'requests', 'is_active' => true]);
        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Published item',
            'slug' => 'published-item',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);
        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Draft item',
            'slug' => 'draft-item',
            'is_active' => false,
        ]);
        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Custom canonical item',
            'slug' => 'custom-canonical-item',
            'canonical_url' => 'https://content.example.test/services/custom-canonical-item',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('content-type', 'application/xml; charset=UTF-8')
            ->assertSee('/notary-services/requests/published-item', false)
            ->assertDontSee('/notary-services/requests/draft-item', false)
            ->assertDontSee('/notary-services/requests/custom-canonical-item', false);
    }

    public function test_changed_service_and_category_slugs_permanently_redirect(): void
    {
        $category = ServiceCategory::create([
            'name' => 'Requests',
            'slug' => 'requests',
            'is_active' => true,
        ]);
        $service = Service::create([
            'service_category_id' => $category->id,
            'title' => 'Published item',
            'slug' => 'published-item',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $service->update(['slug' => 'updated-item']);
        $category->update(['slug' => 'updated-requests']);

        $this->get('/notary-services/service/published-item')
            ->assertStatus(301)
            ->assertRedirect(route('services.show', ['category' => 'updated-requests', 'service' => 'updated-item']));
        $this->get('/notary-services/category/requests')
            ->assertStatus(301)
            ->assertRedirect(route('services.category', 'updated-requests'));
        $this->get('/notary-services/requests/updated-item')
            ->assertStatus(301)
            ->assertRedirect(route('services.show', ['category' => 'updated-requests', 'service' => 'updated-item']));
    }

    public function test_breadcrumbs_and_json_ld_are_generated_safely(): void
    {
        $parent = ServiceCategory::create(['name' => 'Parent', 'slug' => 'parent', 'is_active' => true]);
        $category = ServiceCategory::create([
            'parent_id' => $parent->id,
            'name' => '<script>alert(1)</script>',
            'slug' => 'child',
            'is_active' => true,
        ]);
        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Safe service',
            'slug' => 'safe-service',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $this->get('/notary-services/child/safe-service')
            ->assertOk()
            ->assertSee('application/ld+json', false)
            ->assertSee('Parent')
            ->assertSee('\u003Cscript\u003Ealert(1)\u003C\/script\u003E', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_robots_file_has_admin_disallow_and_current_sitemap_url(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin/', false)
            ->assertSee(route('sitemap'), false);
    }

    public function test_service_search_filters_database_results(): void
    {
        $category = ServiceCategory::create(['name' => 'Requests', 'slug' => 'requests', 'is_active' => true]);
        foreach (['Power of Attorney', 'Judicial Warning'] as $index => $title) {
            Service::create([
                'service_category_id' => $category->id,
                'title' => $title,
                'slug' => 'request-'.$index,
                'is_active' => true,
                'published_at' => now()->subMinute(),
            ]);
        }

        $response = $this->get('/notary-services?q=judicial');
        $response->assertOk()->assertSee('Judicial Warning');
        $this->assertSame(1, substr_count($response->getContent(), 'class="service-card"'));
    }

    public function test_admin_controlled_metadata_is_escaped_in_html_attributes(): void
    {
        $category = ServiceCategory::create(['name' => 'Requests', 'slug' => 'requests', 'is_active' => true]);
        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Safe title',
            'slug' => 'safe-title',
            'meta_title' => '"><script>alert(1)</script>',
            'meta_description' => 'Description',
            'canonical_url' => 'https://canonical.example.test/safe-title',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $this->get('/notary-services/requests/safe-title')
            ->assertOk()
            ->assertSee('href="https://canonical.example.test/safe-title"', false)
            ->assertSee('"url":"https:\/\/canonical.example.test\/safe-title"', false)
            ->assertSee('&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
