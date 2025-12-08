<?php

use App\Services\CartService;
use App\Core\Auth;
use App\Repositories\CategoryRepository;

// Категорії для навбару: якщо вже передані в $navCategories / $categories – використовуємо їх
$navCategories = $navCategories ?? ($categories ?? null);

if ($navCategories === null) {
    try {
        $categoryRepo  = new CategoryRepository();
        $navCategories = $categoryRepo->getRootCategories();
        $navCategories = array_slice($navCategories, 0, 14, true);
    } catch (\Throwable $e) {
        $navCategories = [];
    }
}

$cartCount = CartService::countItems();
$isLogged  = Auth::check();
$user      = $isLogged ? Auth::user() : null;
$userRoles = $user['roles'] ?? [];

// Для підсвітки активного пункту меню
$currentPage = $page ?? ''; // 'home' / 'products' / 'categories' / 'brands' / 'support' / 'admin'

// Визначаємо, чи ми в адмінській зоні
$requestUri   = $_SERVER['REQUEST_URI'] ?? '';
$isAdminRole  = in_array('admin', $userRoles, true)
    || in_array('manager', $userRoles, true)
    || in_array('content_manager', $userRoles, true);
$isAdminArea  = (strpos($requestUri, '/admin') === 0) && $isAdminRole;
?>

<?php if ($isAdminArea): ?>
    <!-- ======================
         АДМІНСЬКИЙ НАВБАР
         Оновлений логотип + профіль + перемикач мови
         ====================== -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-warning sticky-top">
        <div class="container-fluid py-2">

            <!-- Бренд адмінки -->
            <a class="navbar-brand d-flex align-items-center me-3" href="/admin">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-2"
                     style="
                        width:40px;
                        height:40px;
                        background-color:#212529;
                        border:2px solid #ffc107;
                     ">
                    <i class="bi bi-gear-wide-connected text-warning"
                       style="font-size:1.1rem; line-height:1;"></i>
                </div>
                <div class="d-flex flex-column lh-1 text-start">
                    <span class="fw-semibold" style="letter-spacing:.03em;">
                        AutoParts <span class="text-warning">Admin</span>
                    </span>
                </div>
            </a>

            <!-- Тоглер -->
            <button class="navbar-toggler border-0" type="button"
                    data-bs-toggle="collapse" data-bs-target="#adminNav"
                    aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNav">
                <!-- ЦЕНТРАЛЬНИЙ РЯД: навігація + кнопка "У магазин" -->
                <ul class="navbar-nav mx-auto align-items-lg-center gap-lg-1">

                    <!-- Дашборд -->
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'admin' ? 'active fw-semibold' : '' ?>"
                           href="/admin">
                            <i class="bi bi-speedometer2 me-1"></i> <?= __('admin.nav.dashboard'); ?>
                        </a>
                    </li>

                    <!-- Замовлення -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= $currentPage === 'admin_orders' ? 'active fw-semibold' : '' ?>"
                           href="#" id="adminOrdersDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-receipt me-1"></i> <?= __('admin.nav.orders'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminOrdersDropdown">
                            <li>
                                <a class="dropdown-item" href="/admin/orders?status=pending">
                                    <i class="bi bi-hourglass-split me-1 text-warning"></i>
                                    <?= __('admin.nav.orders.pending'); ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/admin/orders?status=paid">
                                    <i class="bi bi-check2-circle me-1 text-success"></i>
                                    <?= __('admin.nav.orders.paid'); ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/admin/orders?status=shipped">
                                    <i class="bi bi-truck me-1 text-info"></i>
                                    <?= __('admin.nav.orders.shipped'); ?>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="/admin/orders">
                                    <i class="bi bi-list-ul me-1"></i>
                                    <?= __('admin.nav.orders.all'); ?>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Каталог -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= $currentPage === 'admin_catalog' ? 'active fw-semibold' : '' ?>"
                           href="#" id="adminCatalogDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-box-seam me-1"></i> <?= __('admin.nav.catalog'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminCatalogDropdown">
                            <li>
                                <a class="dropdown-item" href="/admin/products">
                                    <i class="bi bi-boxes me-1"></i> <?= __('admin.nav.catalog.products'); ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/admin/categories">
                                    <i class="bi bi-grid-3x3-gap me-1"></i> <?= __('admin.nav.catalog.categories'); ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/admin/brands">
                                    <i class="bi bi-badge-tm me-1"></i> <?= __('admin.nav.catalog.brands'); ?>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Центральна кнопка "У магазин" -->
                    <li class="nav-item mx-lg-3 my-2 my-lg-0">
                        <a href="/"
                           class="btn btn-warning rounded-pill px-4 py-2 fw-semibold shadow-sm d-flex align-items-center text-dark">
                            <i class="bi bi-shop me-2 fs-5"></i>
                            <span><?= __('admin.nav.to_shop'); ?></span>
                        </a>
                    </li>

                    <!-- Маркетинг -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle"
                           href="#" id="adminMarketingDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-megaphone me-1"></i> <?= __('admin.nav.marketing'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminMarketingDropdown">
                            <li>
                                <a class="dropdown-item" href="/admin/discounts">
                                    <i class="bi bi-percent me-1 text-success"></i>
                                    <?= __('admin.nav.marketing.discounts'); ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/admin/banners">
                                    <i class="bi bi-image me-1"></i>
                                    <?= __('admin.nav.marketing.banners'); ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/admin/pages">
                                    <i class="bi bi-file-earmark-text me-1"></i>
                                    <?= __('admin.nav.marketing.pages'); ?>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Відгуки / підтримка -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle"
                           href="#" id="adminSupportDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-chat-dots me-1"></i> <?= __('admin.nav.support'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminSupportDropdown">
                            <li>
                                <a class="dropdown-item" href="/admin/reviews/pending">
                                    <i class="bi bi-chat-left-text me-1 text-info"></i>
                                    <?= __('admin.nav.support.reviews_pending'); ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/admin/support?status=open">
                                    <i class="bi bi-life-preserver me-1 text-danger"></i>
                                    <?= __('admin.nav.support.tickets_open'); ?>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Користувачі -->
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/users">
                            <i class="bi bi-people me-1"></i> <?= __('admin.nav.users'); ?>
                        </a>
                    </li>
                </ul>

                <!-- Праворуч: ПРОФІЛЬ + ПЕРЕМИКАЧ МОВИ -->
                <ul class="navbar-nav ms-lg-0 ms-xl-2 mt-2 mt-lg-0 align-items-center gap-2">
                    <?php
                    // Перемикач мови для адмінки
                    $currentLocale    = $locale ?? 'uk';
                    $currentLangTitle = $currentLocale === 'en' ? 'Eng' : 'Укр';
                    ?>
                    <li class="nav-item dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle"
                                type="button" id="adminLangDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($currentLangTitle, ENT_QUOTES, 'UTF-8'); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="adminLangDropdown">
                            <li>
                                <a class="dropdown-item<?= $currentLocale === 'uk' ? ' active' : ''; ?>"
                                   href="?lang=uk">
                                    Українська
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item<?= $currentLocale === 'en' ? ' active' : ''; ?>"
                                   href="?lang=en">
                                    English
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<?php else: ?>
    <!-- ======================
         КОРИСТУВАЦЬКИЙ НАВБАР (МАГАЗИН)
         Оновлена верстка + мінімалістичний логотип
         ====================== -->
    <header class="sticky-top">

        <!-- TOP BAR: ЛОГО + ПОШУК + ПРОФІЛЬ / КОШИК -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
            <div class="container py-3">

                <!-- Логотип магазину -->
                <a class="navbar-brand d-flex align-items-center me-4" href="/">
                    <div class="d-flex align-items-center justify-content-center rounded-circle me-3"
                         style="
                            width:44px;
                            height:44px;
                            background-color:#212529;
                            border:2px solid #ffc107;
                         ">
                        <i class="bi bi-gear-wide-connected text-warning" style="font-size:1.4rem;"></i>
                    </div>
                    <div class="d-flex flex-column lh-1">
                        <span class="fw-bold" style="font-size:1.25rem; letter-spacing:.04em;">
                            Auto<span class="text-warning">Parts</span>
                        </span>
                        <span class="small text-muted mt-1">
                            <?= __('brand.shop_tagline', 'Інтернет-магазин автозапчастин'); ?>
                        </span>
                    </div>
                </a>

                <!-- Бургер (мобільний) -->
                <button class="navbar-toggler border-0" type="button"
                        data-bs-toggle="collapse" data-bs-target="#topNavContent"
                        aria-controls="topNavContent" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Вміст top-bar -->
                <div class="collapse navbar-collapse mt-3 mt-lg-0" id="topNavContent">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center w-100 gap-3">

                        <!-- ПОШУК -->
                        <form action="/products" method="get"
                              class="flex-grow-1 order-2 order-lg-1">
                            <div class="input-group" style="max-width: 780px;">

                                <!-- Вибір категорій -->
                                <div class="dropdown">
                                    <button class="btn btn-light border border-end-0 dropdown-toggle d-flex align-items-center px-3"
                                            type="button" id="searchCategoriesDropdown"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-grid-3x3-gap me-2"></i>
                                        <span class="small"><?= __('shop.search.top_categories'); ?></span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="searchCategoriesDropdown">
                                        <li>
                                            <a class="dropdown-item" href="/categories">
                                                <?= __('shop.search.all_categories'); ?>
                                            </a>
                                        </li>
                                        <?php if (!empty($navCategories)): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <?php foreach ($navCategories as $cat): ?>
                                                <?php
                                                $catSlug = $cat['slug'] ?? $cat['code'] ?? (string)$cat['id'];
                                                ?>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="/categories/<?= rawurlencode($catSlug); ?>">
                                                        <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>

                                <!-- Поле пошуку -->
                                <input type="search"
                                       name="q"
                                       class="form-control border-start-0 border-end-0"
                                       placeholder="<?= __('shop.search.placeholder'); ?>"
                                       aria-label="<?= __('shop.search.placeholder'); ?>">

                                <!-- Кнопка пошуку -->
                                <button class="btn btn-warning px-4 fw-semibold" type="submit">
                                    <i class="bi bi-search me-1"></i>
                                    <span class="d-none d-md-inline"><?= __('shop.search.submit'); ?></span>
                                </button>
                            </div>
                        </form>

                        <!-- ПРОФІЛЬ / WISHLIST / КОШИК -->
                        <div class="d-flex align-items-center gap-3 ms-lg-auto order-1 order-lg-2">

                            <!-- Профіль / логін -->
                            <?php if ($isLogged): ?>
                                <div class="dropdown">
                                    <button
                                        class="btn btn-outline-secondary d-flex align-items-center rounded-pill px-3 py-1"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-person fs-5 me-2"></i>
                                        <span class="small">
                                            <?= htmlspecialchars($user['login'] ?? __('user.menu.profile_short'), ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                        <i class="bi bi-chevron-down ms-1 small d-none d-lg-inline"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item" href="/profile">
                                                <i class="bi bi-person me-2"></i><?= __('user.menu.profile'); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="/orders">
                                                <i class="bi bi-receipt me-2"></i><?= __('user.menu.orders'); ?>
                                            </a>
                                        </li>
                                        <?php if ($isAdminRole): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="/admin">
                                                    <i class="bi bi-speedometer2 me-2"></i><?= __('user.menu.admin_panel'); ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="/logout">
                                                <i class="bi bi-box-arrow-right me-2"></i><?= __('user.menu.logout'); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <a href="/login"
                                   class="btn btn-outline-secondary d-flex align-items-center rounded-pill px-3 py-1">
                                    <i class="bi bi-person fs-5 me-2"></i>
                                    <span class="small"><?= __('auth.login_register_button'); ?></span>
                                </a>
                            <?php endif; ?>

                            <!-- Wishlist -->
                            <a href="/profile/wishlist"
                               class="btn btn-link text-dark position-relative d-none d-md-inline-flex">
                                <i class="bi bi-heart fs-5"></i>
                            </a>

                            <!-- Кошик -->
                            <a href="/cart"
                               class="btn btn-warning d-flex align-items-center rounded-pill px-3 py-1 position-relative">
                                <i class="bi bi-cart3 fs-5 me-2"></i>
                                <span class="small fw-semibold"><?= __('shop.nav.cart'); ?></span>
                                <?php if ($cartCount > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?= (int)$cartCount; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- BOTTOM BAR: темний бар + жовті акценти, центровані пункти між "Каталогом" та мовою -->
        <nav class="bg-dark border-top border-warning">
            <div class="container">
                <div class="d-flex align-items-center py-2 gap-2">

                    <!-- Кнопка "Каталог" -->
                    <div class="dropdown">
                        <button class="btn btn-warning btn-sm d-flex align-items-center px-3 text-dark fw-semibold"
                                type="button" id="bottomCatalogDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-list me-2"></i>
                            <span class="text-uppercase small"><?= __('shop.nav.catalog_btn'); ?></span>
                            <i class="bi bi-chevron-down ms-1 small"></i>
                        </button>
                        <ul class="dropdown-menu mt-1">
                            <li>
                                <a class="dropdown-item" href="/products">
                                    <?= __('shop.nav.catalog_all_products'); ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/categories">
                                    <?= __('shop.nav.catalog_all_categories'); ?>
                                </a>
                            </li>
                            <?php if (!empty($navCategories)): ?>
                                <li><hr class="dropdown-divider"></li>
                                <?php foreach ($navCategories as $cat): ?>
                                    <?php
                                    $catSlug = $cat['slug'] ?? $cat['code'] ?? (string)$cat['id'];
                                    ?>
                                    <li>
                                        <a class="dropdown-item"
                                           href="/categories/<?= rawurlencode($catSlug); ?>">
                                            <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Центр: навігаційні пункти -->
                    <div class="flex-grow-1 d-flex justify-content-center">
                        <ul class="nav flex-nowrap align-items-center gap-3">
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $currentPage === 'home' ? 'text-warning fw-semibold' : 'text-light' ?>"
                                   href="/" <?= $currentPage === 'home' ? 'aria-current="page"' : '' ?>>
                                    <?= __('nav.home'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $currentPage === 'products' ? 'text-warning fw-semibold' : 'text-light' ?>"
                                   href="/products" <?= $currentPage === 'products' ? 'aria-current="page"' : '' ?>>
                                    <?= __('nav.shop'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $currentPage === 'categories' ? 'text-warning fw-semibold' : 'text-light' ?>"
                                   href="/categories" <?= $currentPage === 'categories' ? 'aria-current="page"' : '' ?>>
                                    <?= __('nav.categories'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $currentPage === 'brands' ? 'text-warning fw-semibold' : 'text-light' ?>"
                                   href="/brands" <?= $currentPage === 'brands' ? 'aria-current="page"' : '' ?>>
                                    <?= __('nav.brands'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $currentPage === 'support' ? 'text-warning fw-semibold' : 'text-light' ?>"
                                   href="/support" <?= $currentPage === 'support' ? 'aria-current="page"' : '' ?>>
                                    <?= __('nav.support'); ?>
                                </a>
                            </li>
                            <?php if ($isAdminRole): ?>
                                <li class="nav-item">
                                    <a class="nav-link py-1 px-3 small <?= $currentPage === 'admin' ? 'text-danger fw-semibold' : 'text-light' ?>"
                                       href="/admin">
                                        <?= __('nav.admin'); ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Праворуч: мова -->
                    <?php
                    $currentLocale    = $locale ?? 'uk';
                    $currentLangTitle = $currentLocale === 'en' ? 'English' : 'Українська';
                    ?>
                    <div class="dropdown ms-2">
                        <button class="btn btn-outline-light dropdown-toggle btn-sm" type="button"
                                id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($currentLangTitle, ENT_QUOTES, 'UTF-8'); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                            <li>
                                <a class="dropdown-item<?= $currentLocale === 'uk' ? ' active' : '' ?>"
                                   href="?lang=uk">
                                    Українська
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item<?= $currentLocale === 'en' ? ' active' : '' ?>"
                                   href="?lang=en">
                                    English
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </nav>
    </header>
<?php endif; ?>
