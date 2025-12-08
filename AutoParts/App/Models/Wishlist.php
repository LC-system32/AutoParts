<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Wishlist Model
 */
class Wishlist extends Model
{
    /**
     * Get the wishlist for the current user
     */
    public static function getWishlist(): array
    {
        return self::getList('/api/wishlist');
    }

    /**
     * Add a product to the wishlist
     */
    public static function add(int $productId): array
    {
        return self::create('/api/wishlist/items', [
            'product_id' => $productId,
        ]);
    }

    /**
     * Remove a product from the wishlist
     */
    public static function remove(int $productId): array
    {
        return self::delete('/api/wishlist/items/' . $productId);
    }
}