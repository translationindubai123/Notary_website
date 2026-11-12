<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::updated(function (Service $service) {
            if ($service->wasChanged('slug')) {
                SlugRedirect::remember('service', $service->getOriginal('slug'), $service->id);
            }
        });

        static::deleted(function (Service $service) {
            SlugRedirect::query()
                ->where('entity_type', 'service')
                ->where('target_id', $service->id)
                ->delete();
        });
    }

    protected $fillable = [
        'service_category_id', 'title', 'slug', 'short_description', 'introduction',
        'description', 'eligibility', 'processing_time', 'service_availability',
        'important_notes', 'who_needs_service', 'meta_title', 'meta_description',
        'canonical_url', 'og_title', 'og_description', 'featured_image', 'image_alt',
        'sort_order', 'is_featured', 'is_active', 'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ServiceDocument::class)->orderBy('sort_order')->orderBy('id');
    }

    public function processSteps(): HasMany
    {
        return $this->hasMany(ServiceProcessStep::class)->orderBy('sort_order')->orderBy('id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ServiceFaq::class)->orderBy('sort_order')->orderBy('id');
    }

    public function relatedServices(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'service_related_services', 'service_id', 'related_service_id')
            ->withTimestamps();
    }

    public function relatedFromServices(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'service_related_services', 'related_service_id', 'service_id')
            ->withTimestamps();
    }

    public function publicUrl(): string
    {
        $route = $this->category->service_type === 'moj' ? 'moj.services.show' : 'services.show';

        return route($route, ['category' => $this->category->slug, 'service' => $this->slug]);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereHas('category', function (Builder $categoryQuery) {
                $categoryQuery->where('is_active', true);
            });
    }
}
