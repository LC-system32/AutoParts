<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Product Model
 */
class Product extends Model
{
    public static function paginate(array $filters = []): array
    {
        return self::getList('/api/products', $filters);
    }

    public static function findBySlug(string $slug): ?array
    {
        try {
            return self::get('/api/products/' . urlencode($slug));
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getFitments(int $productId): array
    {
        try {
            return self::get('/api/products/' . $productId . '/fitments');
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function getOffers(int $productId, ?string $sort = null): array
    {
        $params = [];
        if ($sort) {
            $params['sort'] = $sort;
        }
        try {
            return self::get('/api/products/' . $productId . '/offers', $params);
        } catch (\Throwable $e) {
            return [];
        }
    }
}