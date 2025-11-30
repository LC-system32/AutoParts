<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Order Model
 */
class Order extends Model
{
    /**
     * Create a new order
     *
     * The payload should include at minimum a cart_id. Optionally
     * delivery_method_id, payment_method_id, address_id, discount_code.
     */
    public static function createOrder(array $payload): array
    {
        return self::create('/api/orders', $payload);
    }

    /**
     * Get orders for the current user
     */
    public static function listForCurrentCustomer(): array
    {
        // 1) Залогінений користувач – як було
        if (!empty($_SESSION['user']['id'])) {
            return self::getList('/api/orders', [
                'user_id' => (int)$_SESSION['user']['id'],
            ]);
        }

        // 2) Гість – шукаємо email / phone з останнього чекаута
        $email = $_SESSION['checkout']['email'] ?? null;
        $phone = $_SESSION['checkout']['phone'] ?? null;

        if (empty($email) && empty($phone)) {
            // Немає даних – показувати нічого
            return [];
        }

        return self::getList('/api/orders', [
            'email' => $email,
            'phone' => $phone,
        ]);
    }
}
