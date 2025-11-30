<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\ApiClient;

final class Review
{
    /**
     * Отримати список відгуків для товару через API.
     *
     * Підтримує 2 варіанти відповіді від ApiClient:
     *  1) ['success' => true, 'data' => [...]]
     *  2) просто масив відгуків [...], без success
     */
    public static function listForProduct(int $productId): array
    {
        if ($productId <= 0) {
            return [];
        }

        $resp = ApiClient::get("/api/products/{$productId}/reviews");

        // На всякий випадок залогуємо, щоб бачити реальну структуру
        // (можеш потім вимкнути)
        if (!is_array($resp)) {
            error_log('Review::listForProduct API non-array response: ' . print_r($resp, true));
            return [];
        }

        // Випадок 1: ApiClient повертає "повну" відповідь { success, data }
        if (array_key_exists('success', $resp)) {
            if (empty($resp['success'])) {
                $errMsg = isset($resp['error']) ? (string)$resp['error'] : 'Unknown API error';
                error_log('Review::listForProduct API error: ' . $errMsg);
                return [];
            }

            $data = $resp['data'] ?? [];
            return is_array($data) ? $data : [];
        }

        // Випадок 2: ApiClient вже повернув "data" напряму
        // (масив відгуків без ключа success)
        return $resp;
    }

    /**
     * Створити відгук через API.
     *
     * Підтримує 2 варіанти відповіді ApiClient::post():
     *  1) ['success' => true, 'data' => [...]]
     *  2) просто масив відгуку [...], без success
     *
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

        // Лог для дебагу – щоб побачити, що саме повертає ApiClient
        error_log('Review::createReview API raw response: ' . print_r($resp, true));

        // Якщо взагалі не масив – це точно помилка
        if (!is_array($resp)) {
            throw new \RuntimeException('API error: Empty or invalid API response');
        }

        // Випадок 1: ApiClient повернув "повну" відповідь з success
        if (array_key_exists('success', $resp)) {
            if (empty($resp['success'])) {
                $errMsg = isset($resp['error']) ? (string)$resp['error'] : 'Unknown API error';
                throw new \RuntimeException('API error: ' . $errMsg);
            }

            $data = $resp['data'] ?? [];
            return is_array($data) ? $data : [];
        }

        // Випадок 2: ApiClient вже віддав готовий обʼєкт відгуку (без success)
        // Наприклад: ['id' => 4, 'product_id' => 1, ...]
        if (isset($resp['id']) && isset($resp['product_id'])) {
            return $resp;
        }

        // Якщо структура дивна – кидаємо помилку
        throw new \RuntimeException('API error: Unexpected response structure');
    }
}
