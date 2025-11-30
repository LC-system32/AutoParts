<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;

/**
 * ProductRepository
 *
 * Репозиторій для зручного отримання списків товарів
 * через існуючу модель Product, яка вже ходить в API.
 */
class ProductRepository
{
    /**
     * Базовий метод-обгортка над Product::paginate()
     * для довільних фільтрів.
     *
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function paginate(array $filters = []): array
    {
        // Product::paginate повертає масив виду:
        // ['items' => [...], 'pagination' => [...]]
        return Product::paginate($filters);
    }

    /**
     * "Топ-діли" для головної сторінки.
     *
     * Тут ми просто беремо товари з пагінації з певним сортуванням.
     * Якщо в Node API є окреме сортування по знижках — можна підставити його.
     *
     * @param int $limit
     * @return array<int,array<string,mixed>>
     */
    public function getTopDeals(int $limit = 8): array
    {
        $data = Product::paginate([
            // якщо у твоєму API є sort=discount_desc або типу того — підстав його
            'sort'     => 'price_desc', // тимчасово: дорожчі/цінніші товари
            'per_page' => $limit,
            'page'     => 1,
        ]);

        return $data['items'] ?? [];
    }

    /**
     * Популярні товари для секції "Популярні" на головній.
     *
     * @param int $limit
     * @return array<int,array<string,mixed>>
     */
    public function getPopular(int $limit = 8): array
    {
        $data = Product::paginate([
            // у productModel.js я бачив sort 'popular' / 'newest' — використай те, що є
            'sort'     => 'popular', // якщо немає — API просто проігнорує і дасть дефолт
            'per_page' => $limit,
            'page'     => 1,
        ]);

        return $data['items'] ?? [];
    }

    /**
     * Останні додані товари (на випадок, якщо треба на головну / в окремий блок).
     *
     * @param int $limit
     * @return array<int,array<string,mixed>>
     */
    public function getLatest(int $limit = 8): array
    {
        $data = Product::paginate([
            'sort'     => 'newest',
            'per_page' => $limit,
            'page'     => 1,
        ]);

        return $data['items'] ?? [];
    }
}
