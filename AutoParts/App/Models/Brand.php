<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Brand Model
 *
 * Provides access to brand resources via the Node API.
 */
class Brand extends Model
{
    /**
     * Get all brands
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return self::getList('/api/brands');
    }

    /**
     * Find a brand by slug
     *
     * @param string $slug Brand slug
     *
     * @return array<string, mixed>|null
     */
    public static function findBySlug(string $slug): ?array
    {
        try {
            return self::get('/api/brands/' . urlencode($slug));
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Admin: create a new brand
     */
    public static function createBrand(array $data): array
    {
        return self::create('/api/brands', $data);
    }

    /**
     * Admin: update an existing brand
     */
    public static function update(string $slug, array $data): array
    {
        return self::update('/api/brands/' . urlencode($slug), $data);
    }

    /**
     * Admin: delete a brand
     */
    public static function delete(string $slug): array
    {
        return self::delete('/api/brands/' . urlencode($slug));
    }
}
