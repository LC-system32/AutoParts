<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Address Model
 *
 * Provides convenience methods for interacting with the user's addresses
 * via the Node.js API. All requests are authenticated using the
 * session token.
 */
class Address extends Model
{
    /**
     * Fetch all addresses belonging to the current user.
     *
     * @return array<int, array<string,mixed>>
     */
    public static function all(): array
    {
        return self::getList('/api/addresses');
    }

    /**
     * Fetch a single address by its ID. Returns null if not found.
     *
     * @param int $id
     * @return array<string,mixed>|null
     */
    public static function find(int $id): ?array
    {
        try {
            $result = self::get('/api/addresses/' . $id);
            return $result ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }
    /**
     * Create a new address
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public static function createForUser(array $data): array
    {
        return parent::create('/api/addresses', $data);
    }

    /**
     * Update an existing address
     *
     * @param int $id
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public static function updateAddress(int $id, array $data): array
    {
        return \App\Core\ApiClient::put('/api/addresses/' . $id, $data);
    }

    /**
     * Delete an address by ID
     *
     * @param int $id
     * @return array<string,mixed>
     */
    public static function deleteAddress(int $id): array
    {
        return parent::delete('/api/addresses/' . $id);
    }
}
