<?php

namespace App\Providers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $settings = SiteSetting::values();
            $view->with('siteSettings', array_merge([
                'site_name' => config('site.name'),
                'site_description' => config('site.description'),
                'home_hero_eyebrow' => 'PROFESSIONAL DOCUMENT SERVICES · DUBAI, UAE',
                'home_hero_title' => 'Notary Services in Dubai for Personal and Business Documents',
                'home_hero_introduction' => 'Find the right notary service for your document. Explore powers of attorney, company documents, legal notices and other notarial services, with clear information to help you understand the next steps.',
                'home_meta_description' => 'Looking for notary services in Dubai? Explore document notarisation, powers of attorney, company documents, legal notices and other notary services. Contact our team for guidance.',
                'home_primary_cta' => 'Explore Our Services',
                'home_secondary_cta' => 'Contact Our Team',
                'site_phone' => config('site.phone'),
                'site_whatsapp' => config('site.whatsapp'),
                'site_email' => config('site.email'),
                'site_address' => config('site.address'),
                'site_logo' => null,
                'site_office_hours' => config('site.office_hours'),
                'moj_title' => config('site.moj_title'),
                'moj_introduction' => config('site.moj_introduction'),
                'moj_body' => '',
            ], $settings));
        });

        View::composer('layouts.app', function ($view) {
            $categories = ServiceCategory::active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'parent_id', 'service_type', 'name', 'slug']);
            $servicesByCategory = Service::published()
                ->whereIn('service_category_id', $categories->modelKeys())
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get(['id', 'service_category_id', 'title', 'slug'])
                ->groupBy('service_category_id')
                ->map(function (Collection $services) {
                    return $services->take(5);
                });
            $categoriesByParent = $categories->groupBy('parent_id');
            $buildCategoryTree = function ($parentId, string $type) use (&$buildCategoryTree, $categoriesByParent, $servicesByCategory) {
                return $categoriesByParent->get($parentId, collect())
                    ->where('service_type', $type)
                    ->map(function (ServiceCategory $category) use (&$buildCategoryTree, $servicesByCategory, $type) {
                        $category->setRelation('navigation_services', $servicesByCategory->get($category->id, collect()));
                        $category->setRelation('navigation_children', $buildCategoryTree($category->id, $type));

                        return $category;
                    });
            };

            $view->with('footerCategories', ServiceCategory::active()
                ->where('service_type', 'notary')
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->take(5)
                ->get(['name', 'slug']))
                ->with('navigationCategories', $buildCategoryTree(null, 'notary'))
                ->with('mojNavigationCategories', $buildCategoryTree(null, 'moj'));
        });
    }
}
