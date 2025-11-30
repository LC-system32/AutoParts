<?php

use App\Controllers\OrderController;
use App\Controllers\HomeController;
use App\Controllers\BrandController;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\WishlistController;
use App\Controllers\SupportController;
use App\Controllers\AddressController;
use App\Controllers\InfoController;
use App\Controllers\AdminController;
use App\Controllers\CarController;


/** @var \App\Core\Router $router */

// Public pages
$router->get('/', [HomeController::class, 'index']);

// Brands
$router->get('/brands', [BrandController::class, 'index']);
$router->get('/brands/{slug}', [BrandController::class, 'show']);

// Categories
$router->get('/categories', [CategoryController::class, 'index']);
$router->get('/categories/{slug}', [CategoryController::class, 'show']);
$router->get('/categories/subcategory/{slug}', [CategoryController::class, 'subcategory']);

// Products
$router->post('/products/{slug}/reviews', [ProductController::class, 'reviewsSubmit']);
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/{slug}', [ProductController::class, 'show']);

// Cart
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);

// Checkout / Orders
$router->get('/checkout', [OrderController::class, 'checkout']);
$router->post('/checkout/submit', [OrderController::class, 'submit']);
$router->get('/orders', [OrderController::class, 'orders']);

// Authentication
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'loginPost']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'registerPost']);
$router->get('/logout', [AuthController::class, 'logout']);

// User profile
$router->get('/profile', [ProfileController::class, 'index']);
$router->get('/profile/orders', [ProfileController::class, 'orders']);
$router->get('/profile/wishlist', [ProfileController::class, 'wishlist']);

// Profile edit
$router->get('/profile/edit', [ProfileController::class, 'edit']);
$router->post('/profile/update', [ProfileController::class, 'update']);

// Security / sessions management
$router->get('/profile/sessions', [ProfileController::class, 'sessions']);
$router->post('/profile/sessions/terminate', [ProfileController::class, 'terminateSessions']);

// Profile addresses
$router->get('/profile/addresses', [AddressController::class, 'index']);
$router->get('/profile/addresses/create', [AddressController::class, 'createForUser']);
$router->post('/profile/addresses/store', [AddressController::class, 'store']);
$router->get('/profile/addresses/{id}/edit', [AddressController::class, 'edit']);
$router->post('/profile/addresses/{id}/update', [AddressController::class, 'update']);
$router->post('/profile/addresses/{id}/delete', [AddressController::class, 'delete']);

// Static informational pages
$router->get('/info/faq', [InfoController::class, 'faq']);
$router->get('/info/privacy', [InfoController::class, 'privacy']);
$router->get('/info/about', [InfoController::class, 'about']);
$router->get('/info/payment-delivery', [InfoController::class, 'paymentDelivery']);
$router->get('/info/contact', [InfoController::class, 'contact']);

// Wishlist actions
$router->post('/wishlist/add', [WishlistController::class, 'add']);
$router->post('/wishlist/remove', [WishlistController::class, 'remove']);

// Support (public)
$router->get('/support', [SupportController::class, 'index']);
$router->post('/support', [SupportController::class, 'submit']);
$router->post('/support/reply', [SupportController::class, 'reply']);
// ======================================================================
// ADMIN ROUTES
// ======================================================================

// Admin dashboard
$router->get('/admin', [AdminController::class, 'dashboard']);

// -------------------- Користувачі та ролі -----------------------------
// список
$router->get('/admin/users', [AdminController::class, 'users']);

// сторінка створення
$router->get('/admin/users/create', [AdminController::class, 'usersEdit']);
$router->post('/admin/users/create', [AdminController::class, 'usersCreate']);

// редагування існуючого
$router->get('/admin/users/{id}/edit', [AdminController::class, 'usersEdit']);
$router->post('/admin/users/{id}/update', [AdminController::class, 'usersUpdate']);


$router->get('/admin/users/{id}/sessions', [AdminController::class, 'userSessions']);
$router->post('/admin/users/{id}/sessions/terminate', [AdminController::class, 'userSessionsTerminate']);

// ----------------------- Замовлення ----------------------------------

$router->get('/admin/orders', [AdminController::class, 'orders']);
$router->get('/admin/orders/{id}', [AdminController::class, 'orderShow']);
$router->post('/admin/orders/{id}/status', [AdminController::class, 'orderStatus']);

// ----------------------- Відгуки -------------------------------------

$router->get('/admin/reviews/pending', [AdminController::class, 'reviewsPending']);
$router->post('/admin/reviews/{id}/moderate', [AdminController::class, 'reviewModerate']);
$router->post('/admin/reviews/{id}/approve', [AdminController::class, 'reviewApprove']);
$router->post('/admin/reviews/{id}/delete', [AdminController::class, 'reviewDelete']);

// ----------------------- Підтримка -----------------------------------

$router->get('/admin/support', [AdminController::class, 'support']);
$router->get('/admin/support/{id}', [AdminController::class, 'supportView']);
$router->post('/admin/support/{id}/status', [AdminController::class, 'supportStatus']);
$router->post('/admin/support/{id}/reply', [AdminController::class, 'supportReply']);

// ----------------------- Каталог та маркетинг ------------------------

$router->get('/admin/products', [AdminController::class, 'products']);
$router->get('/admin/categories/{id}/edit', [AdminController::class, 'categoryEdit']);
$router->post('/admin/categories/store', [AdminController::class, 'categoryStore']);
$router->post('/admin/categories/{id}/update', [AdminController::class, 'categoryUpdate']);
$router->post('/admin/categories/{id}/delete', [AdminController::class, 'categoryDelete']);

$router->get('/admin/products/create', [AdminController::class, 'productForm']);
$router->post('/admin/products/store', [AdminController::class, 'productStore']);

// Перегляд конкретного товару
$router->get('/admin/products/{id}', [AdminController::class, 'productShow']);

// Редагування
// РЕДАГУВАННЯ (ОКРЕМИЙ МЕТОД)
$router->get('/admin/products/{id}/edit', [AdminController::class, 'productEdit']);

$router->post('/admin/products/{id}/update', [AdminController::class, 'productUpdate']);

// Бренди
$router->get('/admin/brands', [AdminController::class, 'brands']);

// Категорії
$router->get('/admin/categories', [AdminController::class, 'categories']);

// Знижки та промокоди
$router->get('/admin/discounts', [AdminController::class, 'discounts']);

// Запаси та склади
$router->get('/admin/brands', [AdminController::class, 'brands']);

// створення нового бренду
$router->post('/admin/brands/store', [AdminController::class, 'brandStore']);

// форма редагування
$router->get('/admin/brands/{id}/edit', [AdminController::class, 'brandEdit']);

// оновлення бренду
$router->post('/admin/brands/{id}/update', [AdminController::class, 'brandUpdate']);

// видалення бренду
$router->post('/admin/brands/{id}/delete', [AdminController::class, 'brandDelete']);

$router->get('/admin/discounts',          [AdminController::class, 'discounts']);
$router->get('/admin/discounts/create',   [AdminController::class, 'discountCreate']);
$router->post('/admin/discounts/store',   [AdminController::class, 'discountStore']);
$router->get('/admin/discounts/{id}/edit', [AdminController::class, 'discountEdit']);
$router->post('/admin/discounts/{id}/update', [AdminController::class, 'discountUpdate']);
$router->post('/admin/discounts/{id}/delete', [AdminController::class, 'discountDelete']);
$router->post('/cart/coupon/apply', [\App\Controllers\CartController::class, 'couponApply']);
$router->post('/cart/coupon/remove', [\App\Controllers\CartController::class, 'couponRemove']);
$router->get('/car-search', [CarController::class, 'search']);

$router->get('/car', [CarController::class, 'index']);

// JSON для моделей
$router->get('/car/models', [CarController::class, 'modelsJson']);

// JSON для поколінь
$router->get('/car/generations', [CarController::class, 'generationsJson']);

// JSON для модифікацій
$router->get('/car/modifications', [CarController::class, 'modificationsJson']);
// <a href="/auth/google?lang=uk">Увійти через Google</a>
$router->get('/auth/google', [\App\Controllers\AuthController::class, 'redirectToGoogle']);
$router->get('/auth/google/callback', [\App\Controllers\AuthController::class, 'handleGoogleCallback']);
