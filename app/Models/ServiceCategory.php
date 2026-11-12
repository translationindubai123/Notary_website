<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::updated(function (ServiceCategory $category) {
            if ($category->wasChanged('slug')) {
                SlugRedirect::remember('category', $category->getOriginal('slug'), $category->id);
            }
        });

        static::deleted(function (ServiceCategory $category) {
            SlugRedirect::query()
                ->where('entity_type', 'category')
                ->where('target_id', $category->id)
                ->delete();
        });
    }

    protected $fillable = [
        'parent_id', 'service_type', 'name', 'slug', 'short_description', 'description',
        'meta_title', 'meta_description', 'canonical_url', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class)->orderBy('sort_order')->orderBy('title');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function publicCategoryUrl(): string
    {
        return route($this->service_type === 'moj' ? 'moj.services.category' : 'services.category', $this->slug);
    }

    public function publicServicesUrl(): string
    {
        return route($this->service_type === 'moj' ? 'moj.services.index' : 'services.index');
    }
}
