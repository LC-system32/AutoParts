<?php
declare(strict_types=1);

namespace App\Services;

/**
 * CartService provides helper methods for interacting with the shopping cart.
 *
 * This simplified implementation stores cart data in the PHP session.
 */
class CartService
{
    /**
     * Count total quantity of items in cart.
     */
    public static function countItems(): int
    {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart']['items'])) {
            return 0;
        }
        $count = 0;
        foreach ($_SESSION['cart']['items'] as $qty) {
            $count += (int)$qty;
        }
        return $count;
    }

    /**
     * Add item to cart; increment quantity if already present.
     */
    public static function addItem(int $productId, int $quantity = 1): void
    {
        if (!isset($_SESSION['cart']['items'][$productId])) {
            $_SESSION['cart']['items'][$productId] = 0;
        }
        $_SESSION['cart']['items'][$productId] += max(1, $quantity);
    }
}