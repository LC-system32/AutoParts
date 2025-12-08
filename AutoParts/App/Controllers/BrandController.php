<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Brand;
use App\Models\Product;

/**
 * BrandController
 */
class BrandController extends Controller
{
    /**
     * List all brands
     */
    public function index(): void
    {
        $brands = Brand::all();
        $this->render('brands/index', [
            'pageTitle' => 'Бренди',
            'brands'    => $brands,
        ]);
    }

    /**
     * Show a single brand and its products
     */
    public function show(): void
    {
        $slug = $this->request->routeParam('slug');
        $brand = $slug ? Brand::findBySlug($slug) : null;
        if (!$brand) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Бренд не знайдено']);
            return;
        }
        
        $products = Product::paginate(['brand' => $slug]);
        $this->render('brands/show', [
            'pageTitle' => $brand['name'] ?? 'Бренд',
            'brand'     => $brand,
            'products'  => $products['items'] ?? [],
        ]);
    }
}