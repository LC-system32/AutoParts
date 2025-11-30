<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Product Model
 */
class Product extends Model
{
    /**
     * Paginate products with optional filters
     *
     * Supported filters: search, brand, category, sort, page, per_page
     */
    public static function paginate(array $filters = []): array
    {
        return self::getList('/api/products', $filters);
    }

    /**
     * Find a product by slug
     */
    public static function findBySlug(string $slug): ?array
    {
        try {
            return self::get('/api/products/' . urlencode($slug));
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Отримати список сумісностей (fitments) для товару.
     * Повертає масив об'єктів або порожній масив, якщо сумісностей немає.
     */
    public static function getFitments(int $productId): array
    {
        try {
            return self::get('/api/products/' . $productId . '/fitments');
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Отримати список пропозицій (offers) для товару.
     * Param $sort може бути null або 'cheapest', 'fastest', 'city'.
     */
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