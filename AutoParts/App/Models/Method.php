<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Модель для методів доставки та оплати.
 *
 * Забезпечує отримання списку доступних способів доставки та оплати
 * через Node.js API. Використовується при оформленні замовлення.
 */
class Method extends Model
{
    /**
     * Отримати список активних методів доставки
     *
     * @return array<int, array<string, mixed>>
     */
    public static function delivery(): array
    {
        try {
            return self::get('/api/delivery-methods');
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Отримати список активних методів оплати
     *
     * @return array<int, array<string, mixed>>
     */
    public static function payment(): array
    {
        try {
            return self::get('/api/payment-methods');
        } catch (\Throwable $e) {
            return [];
        }
    }
}