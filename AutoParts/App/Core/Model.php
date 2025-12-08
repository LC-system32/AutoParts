<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base Model
 *
 * Provides simple static helper methods for making requests to the Node.js API.
 * Individual models can extend this class and define specific methods for
 * their domain. Using a base class like this avoids duplication when
 * performing common CRUD operations over HTTP.
 */
abstract class Model
{
    /**
     * Get a list of resources
     */
    protected static function getList(string $endpoint, array $params = []): array
    {
        return ApiClient::get($endpoint, $params);
    }

    /**
     * Get a single resource by path
     */
    protected static function get(string $endpoint, array $params = []): array
    {
        return ApiClient::get($endpoint, $params);
    }

    /**
     * Create a new resource
     */
    protected static function create(string $endpoint, array $data): array
    {
        return ApiClient::post($endpoint, $data);
    }

    /**
     * Update a resource
     */
    protected static function update(string $endpoint, array $data): array
    {
        return ApiClient::patch($endpoint, $data);
    }

    /**
     * Delete a resource
     */
    protected static function delete(string $endpoint): array
    {
        return ApiClient::delete($endpoint);
    }
}
