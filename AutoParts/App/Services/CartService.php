<?php
declare(strict_types=1);

namespace App\Services;

class CartService
{
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
    
    public static function addItem(int $productId, int $quantity = 1): void
    {
        if (!isset($_SESSION['cart']['items'][$productId])) {
            $_SESSION['cart']['items'][$productId] = 0;
        }
        $_SESSION['cart']['items'][$productId] += max(1, $quantity);
    }
}