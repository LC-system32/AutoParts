<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;

/**
 * HomeController
 *
 * Displays the homepage with a hero section and featured brands, categories
 * and products. Adjust the number of items retrieved as needed.
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $categoryRepo = new CategoryRepository();
        $productRepo  = new ProductRepository();

        $categories = $categoryRepo->getRootCategories();
        $brands     = Brand::all();

        $categories = array_slice($categories, 0, 10, true);
        $brands     = array_slice($brands, 0, 8, true);

        $carMakes = Car::getMakes();

        $topDeals        = $productRepo->getTopDeals(8);
        $popularProducts = $productRepo->getPopular(8);

        $this->render('home/index', [
            'page'            => 'home',
            'pageTitle'       => 'Головна',
            'categories'      => $categories,
            'brands'          => $brands,
            'topDeals'        => $topDeals,
            'popularProducts' => $popularProducts,
            'carMakes'        => $carMakes,
        ]);
    }
}
