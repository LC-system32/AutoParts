<?php

/**
 * @var array<int, array<string, mixed>> $products
 * @var array<string, mixed>             $pagination
 * @var array<int, array<string, mixed>> $brands
 * @var array<int, array<string, mixed>> $categories
 * @var array<string, mixed>             $filters
 */

$filters = $filters ?? [];

/**
 * Беремо значення спочатку з $filters, якщо контролер їх заповнює,
 * інакше — напряму з $_GET, щоб сторінка працювала сама по собі.
 */
$searchValue  = (string)($filters['q']        ?? $filters['search'] ?? ($_GET['q']        ?? ''));
$currentBrand = (string)($filters['brand']    ?? ($_GET['brand']    ?? ''));
$currentCat   = (string)($filters['category'] ?? ($_GET['category'] ?? ''));
$currentSort  = (string)($filters['sort']     ?? ($_GET['sort']     ?? 'price_asc'));

$priceMin     = (string)($filters['price_min'] ?? ($_GET['price_min'] ?? ''));
$priceMax     = (string)($filters['price_max'] ?? ($_GET['price_max'] ?? ''));

$inStock      = (string)($filters['in_stock'] ?? ($_GET['in_stock'] ?? ''));
$perPage      = (int)   ($filters['per_page'] ?? ($_GET['per_page'] ?? 12));

/**
 * БАЗОВИЙ МАСИВ ТОВАРІВ ДЛЯ ВІДОБРАЖЕННЯ
 */
$displayProducts = $products ?? [];

/**
 * 1) ФІЛЬТР ПО ТЕКСТОВОМУ ПОШУКУ (name / sku / short_desc)
 *    Працює тільки на сторінці, незалежно від бекенду.
 */
if ($searchValue !== '' && !empty($displayProducts)) {
    $needle = mb_strtolower(trim($searchValue));

    $displayProducts = array_values(array_filter(
        $displayProducts,
        function (array $p) use ($needle) {
            $name       = mb_strtolower((string)($p['name']        ?? ''));
            $sku        = mb_strtolower((string)($p['sku']         ?? ''));
            $shortDesc  = mb_strtolower((string)($p['short_desc']  ?? ''));
            $brandName  = mb_strtolower((string)($p['brand_name']  ?? ''));

            $haystack = $name . ' ' . $sku . ' ' . $shortDesc . ' ' . $brandName;

            return $needle === '' ? true : (mb_strpos($haystack, $needle) !== false);
        }
    ));
}

/**
 * 2) ДОДАТКОВА ФІЛЬТРАЦІЯ ПО ЦІНІ ПРЯМО НА СТОРІНЦІ
 *    (на випадок, якщо бекенд ще не фільтрує)
 */
$minSet = ($priceMin !== '' && is_numeric($priceMin));
$maxSet = ($priceMax !== '' && is_numeric($priceMax));

if ($minSet || $maxSet) {
    $min = $minSet ? (float)$priceMin : null;
    $max = $maxSet ? (float)$priceMax : null;

    $displayProducts = array_values(array_filter(
        $displayProducts,
        function (array $p) use ($minSet, $maxSet, $min, $max) {
            $price = (float)($p['price'] ?? 0);
            if ($minSet && $price < $min) {
                return false;
            }
            if ($maxSet && $price > $max) {
                return false;
            }
            return true;
        }
    ));
}

$totalOnPage  = count($displayProducts);
$totalAll     = isset($pagination['total_items'])
    ? (int)$pagination['total_items']
    : count($products ?? []);

$currentPage = (int)($pagination['current_page'] ?? 1);
$totalPages  = (int)($pagination['total_pages'] ?? 1);
?>

<section class="mb-4">

    <!-- HERO / заголовок сторінки -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body px-3 px-md-4 py-3 py-md-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb small mb-0">
                            <li class="breadcrumb-item">
                                <a href="/" class="text-decoration-none text-muted">
                                    <?= __('nav.home'); ?>
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?= __('nav.catalog'); ?>
                            </li>
                        </ol>
                    </nav>

                    <h1 class="h4 h3-md mb-1 fw-semibold">
                        <?= __('products.list.title'); ?>
                    </h1>
                    <p class="text-muted mb-0">
                        <?= __('products.list.subtitle'); ?>
                    </p>
                </div>

                <div class="text-md-end">
                    <div class="d-inline-flex flex-column align-items-md-end gap-1">
                        <span class="badge rounded-pill text-bg-warning text-dark fw-semibold px-3 py-2">
                            <i class="bi bi-box-seam me-1"></i>
                            <?= __('products.list.on_page_badge', null, [
                                'count' => (string)$totalOnPage,
                            ]); ?>
                        </span>
                        <span class="small text-muted">
                            <?= __('products.list.total_in_catalog', null, [
                                'count' => (string)$totalAll,
                            ]); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ОСНОВНИЙ LAYOUT: зліва фільтри, справа товари -->
    <div class="row g-3 g-md-4">

        <!-- ЛІВА КОЛОНКА: ФІЛЬТРИ -->
        <div class="col-12 col-lg-3">
            <form id="filtersForm" class="card border-0 shadow-sm rounded-4 mb-3 mb-lg-0" method="get" action="/products">
                <div class="card-body p-3 p-md-4">

                    <!-- Пошук -->
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">
                            <?= __('products.list.filters.search_label'); ?>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input
                                type="text"
                                class="form-control border-start-0"
                                name="q"
                                placeholder="<?= __('products.list.filters.search_placeholder'); ?>"
                                value="<?= htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <!-- Бренд -->
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">
                            <?= __('products.list.filters.brand_label'); ?>
                        </label>
                        <select class="form-select form-select-sm" name="brand">
                            <option value=""><?= __('products.list.filters.brand_all'); ?></option>
                            <?php foreach ($brands as $brand): ?>
                                <?php $brandSlug = (string)($brand['slug'] ?? ''); ?>
                                <option value="<?= htmlspecialchars($brandSlug, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?= $currentBrand === $brandSlug ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars((string)($brand['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Категорія -->
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">
                            <?= __('products.list.filters.category_label'); ?>
                        </label>
                        <select class="form-select form-select-sm" name="category">
                            <option value=""><?= __('products.list.filters.category_all'); ?></option>
                            <?php foreach ($categories as $category): ?>
                                <?php $catSlug = (string)($category['slug'] ?? ''); ?>
                                <option value="<?= htmlspecialchars($catSlug, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?= $currentCat === $catSlug ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars((string)($category['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Ціна -->
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1 d-flex justify-content-between">
                            <span><?= __('products.list.filters.price_label'); ?></span>
                            <span class="text-muted small">
                                <?= __('products.list.filters.price_hint'); ?>
                            </span>
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input
                                    type="number"
                                    min="0"
                                    step="1"
                                    class="form-control form-control-sm"
                                    name="price_min"
                                    placeholder="0"
                                    value="<?= htmlspecialchars($priceMin, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-6">
                                <input
                                    type="number"
                                    min="0"
                                    step="1"
                                    class="form-control form-control-sm"
                                    name="price_max"
                                    placeholder="5000"
                                    value="<?= htmlspecialchars($priceMax, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- В наявності -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="in_stock"
                                name="in_stock"
                                value="1"
                                <?= $inStock === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label small" for="in_stock">
                                <i class="bi bi-check2-circle text-success me-1"></i>
                                <?= __('products.list.filters.in_stock_label'); ?>
                            </label>
                        </div>
                    </div>

                    <!-- К-сть на сторінці -->
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">
                            <?= __('products.list.filters.per_page_label'); ?>
                        </label>
                        <select class="form-select form-select-sm" name="per_page">
                            <?php foreach ([12, 24, 36, 48] as $n): ?>
                                <option value="<?= $n; ?>" <?= $perPage === $n ? 'selected' : ''; ?>>
                                    <?= $n; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Поточне сортування як hidden, щоб не губилось при сабміті -->
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($currentSort, ENT_QUOTES, 'UTF-8'); ?>">

                    <!-- Кнопки -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning text-dark fw-semibold">
                            <i class="bi bi-funnel me-1"></i>
                            <?= __('products.list.filters.apply_button'); ?>
                        </button>
                        <a href="/products" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i>
                            <?= __('products.list.filters.reset_button'); ?>
                        </a>
                    </div>

                </div>
            </form>
        </div>

        <!-- ПРАВА КОЛОНКА: СОРТУВАННЯ + ТОВАРИ + ПАГІНАЦІЯ -->
        <div class="col-12 col-lg-9">

            <!-- Верхня панель сортування -->
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body py-2 px-3 px-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">

                        <div class="small text-muted">
                            <?= __('products.list.summary.shown_prefix'); ?>
                            <span class="fw-semibold"><?= $totalOnPage; ?></span>
                            <?= __('products.list.summary.of'); ?>
                            <span class="fw-semibold"><?= $totalAll; ?></span>
                            <?= __('products.list.summary.items'); ?>
                            <?php if ($searchValue !== ''): ?>
                                <?= ' ' . __('products.list.summary.for_query'); ?>
                                «<?= htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8'); ?>»
                            <?php endif; ?>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">
                                <?= __('products.list.sort.label'); ?>
                            </span>
                            <select
                                class="form-select form-select-sm"
                                name="sort"
                                form="filtersForm"
                                onchange="document.getElementById('filtersForm').sort.value=this.value;document.getElementById('filtersForm').submit();">
                                <option value=""><?= __('products.list.sort.default'); ?></option>
                                <option value="price_asc" <?= $currentSort === 'price_asc'   ? 'selected' : ''; ?>>
                                    <?= __('products.list.sort.price_asc'); ?>
                                </option>
                                <option value="price_desc" <?= $currentSort === 'price_desc'  ? 'selected' : ''; ?>>
                                    <?= __('products.list.sort.price_desc'); ?>
                                </option>
                                <option value="name_asc" <?= $currentSort === 'name_asc'    ? 'selected' : ''; ?>>
                                    <?= __('products.list.sort.name_asc'); ?>
                                </option>
                                <option value="name_desc" <?= $currentSort === 'name_desc'   ? 'selected' : ''; ?>>
                                    <?= __('products.list.sort.name_desc'); ?>
                                </option>
                                <option value="newest" <?= $currentSort === 'newest'      ? 'selected' : ''; ?>>
                                    <?= __('products.list.sort.newest'); ?>
                                </option>
                                <option value="popular" <?= $currentSort === 'popular'     ? 'selected' : ''; ?>>
                                    <?= __('products.list.sort.popular'); ?>
                                </option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <!-- СІТКА ТОВАРІВ -->
            <?php if (empty($displayProducts)): ?>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-cart-x fs-1 text-muted"></i>
                        </div>

                        <?php if ($minSet || $maxSet): ?>
                            <h2 class="h5 mb-2">
                                <?= __('products.list.empty.price_range.title'); ?>
                            </h2>
                            <p class="text-muted mb-3">
                                <?= __('products.list.empty.range.prefix'); ?>
                                <?php if ($minSet): ?>
                                    <?= ' ' . __('products.list.empty.range.from'); ?>
                                    <span class="fw-semibold">
                                        <?= htmlspecialchars($priceMin, ENT_QUOTES, 'UTF-8'); ?> ₴
                                    </span>
                                <?php endif; ?>
                                <?php if ($maxSet): ?>
                                    <?php if ($minSet): ?>
                                        <?= ' ' . __('products.list.empty.range.to'); ?>
                                    <?php else: ?>
                                        <?= ' ' . __('products.list.empty.range.to_max'); ?>
                                    <?php endif; ?>
                                    <span class="fw-semibold">
                                        <?= htmlspecialchars($priceMax, ENT_QUOTES, 'UTF-8'); ?> ₴
                                    </span>
                                <?php endif; ?>
                                <?= ' ' . __('products.list.empty.range.suffix'); ?>
                            </p>
                        <?php elseif (
                            $searchValue !== '' ||
                            $currentBrand !== '' ||
                            $currentCat !== '' ||
                            $inStock === '1'
                        ): ?>
                            <h2 class="h5 mb-2">
                                <?= __('products.list.empty.filtered.title'); ?>
                            </h2>
                            <p class="text-muted mb-3">
                                <?= __('products.list.empty.filtered.text'); ?>
                            </p>
                        <?php else: ?>
                            <h2 class="h5 mb-2">
                                <?= __('products.list.empty.empty.title'); ?>
                            </h2>
                            <p class="text-muted mb-3">
                                <?= __('products.list.empty.empty.text'); ?>
                            </p>
                        <?php endif; ?>

                        <a href="/products" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            <?= __('products.list.filters.reset_button'); ?>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4">
                    <?php foreach ($displayProducts as $p): ?>
                        <div class="col d-flex">
                            <?php
                            $product = $p;
                            if (empty($product['slug']) && !empty($product['id'])) {
                                $product['slug'] = (string)$product['id'];
                            }
                            include BASE_PATH . '/public/views/partials/product-card.php';
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- ПАГІНАЦІЯ -->
            <?php if ($totalPages > 1 && !empty($displayProducts)): ?>
                <nav aria-label="<?= __('products.list.pagination.aria'); ?>" class="mt-4">
                    <ul class="pagination justify-content-center mb-0">

                        <?php if ($currentPage > 1): ?>
                            <?php
                            $prevQuery = array_merge($_GET, ['page' => $currentPage - 1]);
                            $prevUrl   = '/products?' . http_build_query($prevQuery);
                            ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="<?= htmlspecialchars($prevUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-label="<?= __('products.list.pagination.prev'); ?>">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php
                            $query = array_merge($_GET, ['page' => $i]);
                            $url   = '/products?' . http_build_query($query);
                            ?>
                            <li class="page-item<?= $i === $currentPage ? ' active' : ''; ?>">
                                <a class="page-link"
                                    href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <?php
                            $nextQuery = array_merge($_GET, ['page' => $currentPage + 1]);
                            $nextUrl   = '/products?' . http_build_query($nextQuery);
                            ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="<?= htmlspecialchars($nextUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-label="<?= __('products.list.pagination.next'); ?>">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</section>