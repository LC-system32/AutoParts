<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Category Model
 */
class Category extends Model
{
    /**
     * Get all categories (optionally hierarchical)
     */
    public static function all(): array
    {
        return self::getList('/api/categories');
    }

    /**
     * Find a category by slug
     */
    public static function findBySlug(string $slug): ?array
    {
        try {
            return self::get('/api/categories/' . urlencode($slug));
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get direct child categories for the given category slug.
     *
     * This returns an array of categories whose parent_id corresponds to the category
     * identified by the provided slug. If no children are found or an error occurs,
     * an empty array is returned.
     *
     * @param string $slug Category slug
     * @return array<int, array<string, mixed>>
     */
    public static function children(string $slug, ?string $search = null): array
    {
        
        $params = [];

        if ($search !== null && $search !== '') {
            
            $params['search'] = $search;
        }

        try {
            
            return self::getList(
                '/api/categories/' . urlencode($slug) . '/children',
                $params
            );
        } catch (\Throwable $e) {
            return [];
        }
    }
}
