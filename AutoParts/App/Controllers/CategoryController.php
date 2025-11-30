<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    /**
     * Список кореневих категорій
     */
    public function index(): void
    {
        $allCategories = Category::all();

        // Беремо тільки батьківські категорії (parent_id = null)
        $rootCategories = array_values(array_filter(
            $allCategories,
            static fn(array $cat): bool => empty($cat['parent_id'])
        ));

        $this->render('categories/index', [
            'pageTitle'  => 'Категорії',
            'categories' => $rootCategories,
        ]);
    }

    /**
     * Список товарів у конкретній (під)категорії
     * /categories/{slug}
     */
    public function show(): void
    {
        $slug = $this->request->routeParam('slug');
        if (!$slug) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Категорію не знайдено']);
            return;
        }

        $category = Category::findBySlug($slug);
        if (!$category) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Категорію не знайдено']);
            return;
        }

        $search = trim($_GET['q'] ?? $_GET['search'] ?? '');

        $filters = ['category' => $slug];
        if ($search !== '') {
            $filters['search'] = $search;
        }

        $productsPage = Product::paginate($filters);

        $this->render('categories/show', [
            'pageTitle' => $category['name'] ?? 'Категорія',
            'category'  => $category,
            'products'  => $productsPage['items'] ?? [],
            'search'    => $search,
        ]);
    }

    /**
     * Список підкатегорій для батьківської категорії
     * /categories/{slug}/children
     */
    public function subcategory(): void
    {
        $slug = $this->request->routeParam('slug');

        if (!$slug) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Категорію не знайдено']);
            return;
        }

        $search = trim($_GET['q'] ?? $_GET['search'] ?? '');

        $category = Category::findBySlug($slug);
        if (!$category) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Категорію не знайдено']);
            return;
        }

        $children = Category::children($slug, $search);

        $this->render('categories/subcategory', [
            'pageTitle' => 'Підкатегорії: ' . ($category['name'] ?? 'Категорія'),
            'category'  => $category,
            'children'  => $children,
            'search'    => $search,
        ]);
    }
}
