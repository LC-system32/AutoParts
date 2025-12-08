<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Order Model
 */
class Order extends Model
{
    public static function createOrder(array $payload): array
    {
        return self::create('/api/orders', $payload);
    }

    public static function listForCurrentCustomer(): array
    {

        if (!empty($_SESSION['user']['id'])) {
            return self::getList('/api/orders', [
                'user_id' => (int)$_SESSION['user']['id'],
            ]);
        }

        $email = $_SESSION['checkout']['email'] ?? null;
        $phone = $_SESSION['checkout']['phone'] ?? null;

        if (empty($email) && empty($phone)) {

            return [];
        }

        return self::getList('/api/orders', [
            'email' => $email,
            'phone' => $phone,
        ]);
    }
}
