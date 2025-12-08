<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use RuntimeException;

final class Cart extends Model
{
    public static function getCartId(): int
    {
        if (!empty($_SESSION['cart_id'])) {
            return (int)$_SESSION['cart_id'];
        }

        $isLoggedIn = !empty($_SESSION['user']['id']);
        if (session_status() !== \PHP_SESSION_ACTIVE) {
            session_start();
        }
        $sessionToken = session_id();

        $cartId = null;
        $lastErr = null;
        
        if ($isLoggedIn) {
            try {
                $res = self::create('/api/carts/mine', []); 
                $cartId = (int)($res['cart']['id'] ?? $res['id'] ?? 0);
            } catch (\Throwable $e) {
                $lastErr = $e;
            }
        }

        if ($cartId <= 0) {
            try {
                $payload = $isLoggedIn
                    ? ['user_id' => (int)$_SESSION['user']['id']]
                    : ['session_token' => (string)$sessionToken];

                $res = self::create('/api/carts', $payload);
                $cartId = (int)($res['cart']['id'] ?? $res['id'] ?? 0);
            } catch (\Throwable $e) {
                $lastErr = $e;
            }
        }

        if ($cartId <= 0) {
            throw new RuntimeException('Failed to create or fetch cart' . ($lastErr ? (': ' . $lastErr->getMessage()) : ''));
        }

        $_SESSION['cart_id'] = $cartId;
        return $cartId;
    }

    public static function getCart(): array
    {
        $cartId = self::getCartId();
        $res    = self::get('/api/carts/' . $cartId);

        $cart = is_array($res['cart'] ?? null) ? $res['cart'] : (is_array($res) ? $res : []);
        $cart['items'] = $cart['items'] ?? [];
        $cart['total'] = $cart['total'] ?? 0;

        return $cart;
    }

    public static function addItem(int $productId, int $quantity): array
    {
        $cartId = self::getCartId();

        return self::create('/api/carts/' . $cartId . '/items', [
            'product_id' => $productId,
            'quantity'   => $quantity,
        ]);
    }

    public static function updateItem(int $itemId, int $quantity): array
    {
        $cartId = self::getCartId();

        return self::update('/api/carts/' . $cartId . '/items/' . $itemId, [
            'quantity' => $quantity,
        ]);
    }

    public static function removeItem(int $itemId): array
    {
        $cartId = self::getCartId();

        return self::delete('/api/carts/' . $cartId . '/items/' . $itemId);
    }

    public static function reset(): void
    {
        unset($_SESSION['cart_id']);
    }
}
