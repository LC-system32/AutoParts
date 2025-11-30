<?php
// file: app/Models/Cart.php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use RuntimeException;

final class Cart extends Model
{
    /** Чому: уникнути хардкоду на кшталт /api/carts/11 і завжди працювати з власним кошиком */
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

        // 1) Спроба 1: якщо логін — /api/carts/mine (де бек сам знаходить/створює кошик)
        if ($isLoggedIn) {
            try {
                $res = self::create('/api/carts/mine', []); // Bearer має бути у HTTP-клієнті Model
                $cartId = (int)($res['cart']['id'] ?? $res['id'] ?? 0);
            } catch (\Throwable $e) {
                $lastErr = $e;
            }
        }

        // 2) Спроба 2: універсальний шлях — /api/carts із payload
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

    /** Забрати кошик; підримує обидві форми відповіді (root або {cart:{...}}). */
    public static function getCart(): array
    {
        $cartId = self::getCartId();
        $res    = self::get('/api/carts/' . $cartId);

        $cart = is_array($res['cart'] ?? null) ? $res['cart'] : (is_array($res) ? $res : []);
        $cart['items'] = $cart['items'] ?? [];
        $cart['total'] = $cart['total'] ?? 0;

        return $cart;
    }

    /** Додати товар. Чому: бек очікує cart_id в URL, інакше 404 */
    public static function addItem(int $productId, int $quantity): array
    {
        $cartId = self::getCartId();

        return self::create('/api/carts/' . $cartId . '/items', [
            'product_id' => $productId,
            'quantity'   => $quantity,
        ]);
    }

    /** Оновити кількість (itemId = productId в бек-роутах) */
    public static function updateItem(int $itemId, int $quantity): array
    {
        $cartId = self::getCartId();

        return self::update('/api/carts/' . $cartId . '/items/' . $itemId, [
            'quantity' => $quantity,
        ]);
    }

    /** Видалити позицію (itemId = productId) */
    public static function removeItem(int $itemId): array
    {
        $cartId = self::getCartId();

        return self::delete('/api/carts/' . $cartId . '/items/' . $itemId);
    }

    /** Рекомендується викликати при логауті/зміні користувача. Чому: не тягнути старий cart_id між акаунтами */
    public static function reset(): void
    {
        unset($_SESSION['cart_id']);
    }
}
