<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>     */
    public function paginate(array $filters = []): array
    {
        return Product::paginate($filters);
    }

    /**
     * @param int $limit
     * @return array<int,array<string,mixed>>
     */
    public function getTopDeals(int $limit = 8): array
    {
        $data = Product::paginate([
            
            'sort'     => 'price_desc', 
            'per_page' => $limit,
            'page'     => 1,
        ]);

        return $data['items'] ?? [];
    }
    /**
     * @param int $limit
     * @return array<int,array<string,mixed>>
     */
    public function getPopular(int $limit = 8): array
    {
        $data = Product::paginate([
            
            'sort'     => 'popular', 
            'per_page' => $limit,
            'page'     => 1,
        ]);

        return $data['items'] ?? [];
    }

    /**
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
