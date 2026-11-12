<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlugRedirect extends Model
{
    protected $fillable = ['entity_type', 'old_slug', 'target_id'];

    public static function remember(string $entityType, string $oldSlug, int $targetId): void
    {
        self::updateOrCreate(
            ['entity_type' => $entityType, 'old_slug' => $oldSlug],
            ['target_id' => $targetId]
        );
    }

    public static function targetId(string $entityType, string $oldSlug): ?int
    {
        $redirect = self::query()
            ->where('entity_type', $entityType)
            ->where('old_slug', $oldSlug)
            ->first();

        return $redirect ? (int) $redirect->target_id : null;
    }
}
