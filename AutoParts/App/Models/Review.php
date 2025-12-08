<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\ApiClient;

final class Review
{
    public static function listForProduct(int $productId): array
    {
        if ($productId <= 0) {
            return [];
        }

        $resp = ApiClient::get("/api/products/{$productId}/reviews");

        if (!is_array($resp)) {
            error_log('Review::listForProduct API non-array response: ' . print_r($resp, true));
            return [];
        }

        if (array_key_exists('success', $resp)) {
            if (empty($resp['success'])) {
                $errMsg = isset($resp['error']) ? (string)$resp['error'] : 'Unknown API error';
                error_log('Review::listForProduct API error: ' . $errMsg);
                return [];
            }

            $data = $resp['data'] ?? [];
            return is_array($data) ? $data : [];
        }

        return $resp;
    }

    /**
     * @param int   $productId
     * @param array $data ['user_id' => int, 'rating' => int, 'title' => ?string, 'body' => string]
     *
     * @throws \RuntimeException
     */
    public static function createReview(int $productId, array $data): array
    {
        if ($productId <= 0) {
            throw new \RuntimeException('Invalid product id');
        }

        $rating = (int)($data['rating'] ?? 0);
        $title  = isset($data['title']) ? (string)$data['title'] : null;
        $body   = trim((string)($data['body'] ?? ''));

        if ($rating < 1 || $rating > 5) {
            throw new \RuntimeException('Rating must be between 1 and 5');
        }
        if ($body === '') {
            throw new \RuntimeException('Review body is required');
        }

        $payload = [
            'rating' => $rating,
            'title'  => $title,
            'body'   => $body,
        ];

        $resp = ApiClient::post("/api/products/{$productId}/reviews", $payload);

        if (!is_array($resp)) {
            throw new \RuntimeException('API error: Empty or invalid API response');
        }

        if (array_key_exists('success', $resp)) {
            if (empty($resp['success'])) {
                $errMsg = isset($resp['error']) ? (string)$resp['error'] : 'Unknown API error';
                throw new \RuntimeException('API error: ' . $errMsg);
            }

            $data = $resp['data'] ?? [];
            return is_array($data) ? $data : [];
        }

        if (isset($resp['id']) && isset($resp['product_id'])) {
            return $resp;
        }

        throw new \RuntimeException('API error: Unexpected response structure');
    }
}
