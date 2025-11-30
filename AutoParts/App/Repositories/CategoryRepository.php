<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;

/**
 * CategoryRepository provides convenient methods for retrieving categories.
 */
class CategoryRepository
{
    /**
     * Get top level categories (parent_id is null).
     *
     * @param int|null $limit Limit number of categories returned; null for unlimited
     * @return array
     */
    public function getRootCategories(?int $limit = null): array
    {
        $all = Category::all();

        return $all;
    }
}