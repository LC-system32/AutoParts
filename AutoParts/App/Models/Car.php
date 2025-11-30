<?php
// file: app/Models/Car.php
declare(strict_types=1);

namespace App\Models;

use App\Core\ApiClient;

final class Car
{
    /**
     * Отримати всі марки авто (car_makes)
     *
     * @return array<int,array<string,mixed>>
     */
  public static function getMakes(): array
    {
        $resp = ApiClient::get('/api/cars/makes');

        if (!is_array($resp)) {
            return [];
        }

        if (function_exists('array_is_list') && array_is_list($resp)) {
            return $resp;
        }

        if (isset($resp['items']) && is_array($resp['items'])) {
            return $resp['items'];
        }
        if (isset($resp['data']) && is_array($resp['data'])) {
            return $resp['data'];
        }

        return [];
    }

    public static function getModelsByMake(int $makeId): array
    {
        if ($makeId <= 0) {
            return [];
        }

        $resp = ApiClient::get('/api/cars/models', [
            'make_id' => $makeId,
        ]);

        if (!is_array($resp)) {
            return [];
        }

        if (function_exists('array_is_list') && array_is_list($resp)) {
            return $resp;
        }

        if (isset($resp['items']) && is_array($resp['items'])) {
            return $resp['items'];
        }
        if (isset($resp['data']) && is_array($resp['data'])) {
            return $resp['data'];
        }

        return [];
    }


    /**
     * Покоління по model_id (car_generations)
     *
     * @param int $modelId
     * @return array<int,array<string,mixed>>
     */
    public static function getGenerationsByModel(int $modelId): array
    {
        if ($modelId <= 0) {
            return [];
        }

        $resp = ApiClient::get('/api/cars/generations', [
            'model_id' => $modelId,
        ]);

        if (!is_array($resp)) {
            return [];
        }

        if (function_exists('array_is_list') && array_is_list($resp)) {
            return $resp;
        }

        if (isset($resp['items']) && is_array($resp['items'])) {
            return $resp['items'];
        }
        if (isset($resp['data']) && is_array($resp['data'])) {
            return $resp['data'];
        }

        return [];
    }

    /**
     * Модифікації по generation_id (car_modifications)
     *
     * @param int $generationId
     * @return array<int,array<string,mixed>>
     */
    public static function getModificationsByGeneration(int $generationId): array
    {
        if ($generationId <= 0) {
            return [];
        }

        $resp = ApiClient::get('/api/cars/modifications', [
            'generation_id' => $generationId,
        ]);

        if (!is_array($resp)) {
            return [];
        }

        if (function_exists('array_is_list') && array_is_list($resp)) {
            return $resp;
        }

        if (isset($resp['items']) && is_array($resp['items'])) {
            return $resp['items'];
        }
        if (isset($resp['data']) && is_array($resp['data'])) {
            return $resp['data'];
        }

        return [];
    }
}
