<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Method extends Model
{
    /**
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