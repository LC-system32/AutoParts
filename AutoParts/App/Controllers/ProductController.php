<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Review;
use App\Core\Auth;
use App\Core\Csrf;

/**
 * ProductController
 */
class ProductController extends Controller
{
    /**
     * Побудувати масив фільтрів з HTTP-запиту
     *
     * @return array<string,mixed>
     */
    private function buildFiltersFromRequest(): array
    {
        $filters = [];

        $search = (string)$this->request->get('q', '');
        if ($search !== '') {
            $filters['search'] = $search;
        }

        $brand = (string)$this->request->get('brand', '');
        if ($brand !== '') {
            $filters['brand'] = $brand;
        }

        $category = (string)$this->request->get('category', '');
        if ($category !== '') {
            $filters['category'] = $category;
        }

        $sort = (string)$this->request->get('sort', '');
        if ($sort !== '') {
            $filters['sort'] = $sort;
        }

        $page = (int)$this->request->get('page', 1);
        if ($page > 0) {
            $filters['page'] = $page;
        }

        $perPage = (int)$this->request->get('per_page', 12);
        if ($perPage > 0) {
            $filters['per_page'] = $perPage;
        }

        $inStock = $this->request->get('in_stock');
        if ($inStock !== null && $inStock !== '') {
            $filters['in_stock'] = $inStock;
        }
  
        $searchType = (string)$this->request->get('search_type', '');
        if ($searchType === 'car') {
            $filters['search_type'] = 'car';

            $makeId         = (int)$this->request->get('make_id', 0);
            $modelId        = (int)$this->request->get('model_id', 0);
            $generationId   = (int)$this->request->get('generation_id', 0);
            $modificationId = (int)$this->request->get('modification_id', 0);

            if ($makeId > 0) {
                $filters['make_id'] = $makeId;
            }
            if ($modelId > 0) {
                $filters['model_id'] = $modelId;
            }
            if ($generationId > 0) {
                $filters['generation_id'] = $generationId;
            }
            if ($modificationId > 0) {
                $filters['modification_id'] = $modificationId;
            }
        }

        return $filters;
    }

    /**
     * Browse products with optional filters (у т.ч. підбір по авто)
     */
    public function index(): void
    {
        $filters = $this->buildFiltersFromRequest();
        $products = Product::paginate($filters);
        $brands   = Brand::all();
        $categories = Category::all();

        $this->render('products/index', [
            'pageTitle'  => 'Каталог товарів',
            'products'   => $products['items'] ?? [],
            'pagination' => $products['pagination'] ?? [],
            'brands'     => $brands,
            'categories' => $categories,
            'filters'    => $filters,
            'searchType'     => $filters['search_type'] ?? null,
            'makeId'         => $filters['make_id'] ?? null,
            'modelId'        => $filters['model_id'] ?? null,
            'generationId'   => $filters['generation_id'] ?? null,
            'modificationId' => $filters['modification_id'] ?? null,
        ]);
    }

    /**
     * Show a single product page
     */
    public function show(): void
    {
        $slug    = $this->request->routeParam('slug');
        $product = $slug ? Product::findBySlug($slug) : null;

        if (!$product) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Товар не знайдено']);
            return;
        }

        $productId = (int)($product['id'] ?? 0);
        $reviews   = [];
        $fitments  = [];
        $offers    = [];

        if ($productId > 0) {
            try {
                $reviews = Review::listForProduct($productId);
            } catch (\Throwable $e) {
                error_log('ProductController::show reviews error: ' . $e->getMessage());
                $reviews = [];
            }

            try {
                $fitments = Product::getFitments($productId);
            } catch (\Throwable $e) {
                error_log('ProductController::show fitments error: ' . $e->getMessage());
                $fitments = [];
            }

            $offersSort = $this->request->get('offers_sort');
            try {
                $offers = Product::getOffers($productId, $offersSort ?: null);
            } catch (\Throwable $e) {
                error_log('ProductController::show offers error: ' . $e->getMessage());
                $offers = [];
            }
        }

        $this->render('products/show', [
            'pageTitle'  => $product['name'] ?? 'Товар',
            'product'    => $product,
            'reviews'    => $reviews,
            'fitments'   => $fitments,
            'offers'     => $offers,
            'offersSort' => $this->request->get('offers_sort'),
        ]);
    }

    /**
     * Submit product review
     */
    public function reviewsSubmit(): void
    {
        $slug    = $this->request->routeParam('slug');
        $product = $slug ? Product::findBySlug($slug) : null;

        if (!$product) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Товар не знайдено']);
            return;
        }

        $token = $this->request->post('_csrf');
        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/products/' . $slug . '#reviews');
        }
        if (!Auth::check()) {
            $this->flash('error', 'Щоб залишити відгук, увійдіть до акаунта.');
            $this->redirect('/login?redirect=' . urlencode('/products/' . $slug . '#reviews'));
        }

        $user = Auth::user();

        $rating = (int)$this->request->post('rating');
        $title  = trim((string)$this->request->post('title', ''));
        $body   = trim((string)$this->request->post('comment', '')); 

        if ($rating < 1 || $rating > 5 || $body === '') {
            $this->flash('error', 'Заповніть текст відгуку та вкажіть оцінку від 1 до 5.');
            $this->redirect('/products/' . $slug . '#reviews');
        }

        try {
            Review::createReview((int)$product['id'], [
                'user_id' => (int)($user['id'] ?? 0),
                'rating'  => $rating,
                'title'   => $title !== '' ? $title : null,
                'body'    => $body,
            ]);

            $this->flash('success', 'Дякуємо! Ваш відгук надіслано на модерацію.');
        } catch (\Throwable $e) {
            error_log('Review create error: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося зберегти відгук: ' . $e->getMessage());
        }

        $this->redirect('/products/' . $slug . '#reviews');
    }
}
