<?php
// file: public/views/categories/products.php

/** @var array<string,mixed>            $category */
/** @var array<int,array<string,mixed>> $products */
/** @var array<string,mixed>            $pagination */
/** @var array<int,array<string,mixed>> $brands */
/** @var array<string,mixed>            $filters */

$filters    = $filters    ?? [];
$pagination = $pagination ?? [];
$brands     = $brands     ?? [];

$searchValue  = (string)($filters['q']        ?? $filters['search'] ?? ($_GET['q']        ?? ''));
$currentBrand = (string)($filters['brand']    ?? ($_GET['brand']    ?? ''));
$currentSort  = (string)($filters['sort']     ?? ($_GET['sort']     ?? 'price_asc'));

$priceMin     = (string)($filters['price_min'] ?? ($_GET['price_min'] ?? ''));
$priceMax     = (string)($filters['price_max'] ?? ($_GET['price_max'] ?? ''));

$inStock      = (string)($filters['in_stock'] ?? ($_GET['in_stock'] ?? ''));
$perPage      = (int)   ($filters['per_page'] ?? ($_GET['per_page'] ?? 12));

$currentCat   = (string)($category['slug'] ?? '');
$categoryPath = '/categories/' . rawurlencode($currentCat);

/* Хелпери: безпечно знижуємо регістр; slugify без залежності від mbstring. */
$lower = function (string $s): string {
    return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
};
$slugify = function (string $s) use ($lower): string {
    $s = $lower(trim($s));
    $s = preg_replace('/[^\p{L}\p{Nd}]+/u', '-', $s) ?? '';
    $s = trim($s, '-');
    if ($s === '') {
        $s = substr(sha1((string)mt_rand() . microtime(true)), 0, 8);
    }
    return $s;
};

/* Якщо бренди не прийшли — зібрати з товарів (підтримка різних форматів) */
if (empty($brands)) {
    $map = [];
    foreach ($products as $p) {
        $slug = '';
        $name = '';

        if (isset($p['brand']) && is_array($p['brand'])) {
            $slug = (string)($p['brand']['slug'] ?? $p['brand']['code'] ?? '');
            $name = (string)($p['brand']['name'] ?? '');
        }

        if ($slug === '' && !empty($p['brand_slug']))  $slug = (string)$p['brand_slug'];
        if ($slug === '' && !empty($p['brand_code']))  $slug = (string)$p['brand_code'];
        if ($name === '' && !empty($p['brand_name']))  $name = (string)$p['brand_name'];
        if ($name === '' && is_string($p['brand'] ?? null)) $name = (string)$p['brand'];

        if ($slug === '' && !empty($p['brand_id'])) {
            $slug = 'id-' . (string)$p['brand_id'];
        }

        if ($slug === '' && $name !== '') {
            $slug = $slugify($name);
        }

        if ($slug === '' || $name === '') continue;

        if (!isset($map[$slug]) || $map[$slug]['name'] === '') {
            $map[$slug] = ['slug' => $slug, 'name' => $name];
        }
    }

    if (!empty($map)) {
        uasort($map, function ($a, $b) {
            return strcasecmp((string)$a['name'], (string)$b['name']);
        });
        $brands = array_values($map);
    }
}

/* Локальна фільтрація по ціні (fallback) */
$displayProducts = $products ?? [];
$minSet = ($priceMin !== '' && is_numeric($priceMin));
$maxSet = ($priceMax !== '' && is_numeric($priceMax));

if ($minSet || $maxSet) {
    $min = $minSet ? (float)$priceMin : null;
    $max = $maxSet ? (float)$priceMax : null;

    $displayProducts = array_values(array_filter(
        $displayProducts,
        function (array $p) use ($minSet, $maxSet, $min, $max) {
            $price = (float)($p['price'] ?? 0);
            if ($minSet && $price < $min) return false;
            if ($maxSet && $price > $max) return false;
            return true;
        }
    ));
}

/* Fallback сортування за знижкою */
if ($currentSort === 'discount') {
    usort($displayProducts, function(array $a, array $b) {
        $ba = isset($a['base_price']) ? (float)$a['base_price'] : 0.0;
        $pa = isset($a['price'])      ? (float)$a['price']      : 0.0;
        $bb = isset($b['base_price']) ? (float)$b['base_price'] : 0.0;
        $pb = isset($b['price'])      ? (float)$b['price']      : 0.0;
        $da = ($ba > 0 && $pa < $ba) ? ($ba - $pa) / $ba : 0.0;
        $db = ($bb > 0 && $pb < $bb) ? ($bb - $pb) / $bb : 0.0;
        return $db <=> $da ?: $pa <=> $pb;
    });
}

$totalOnPage = count($displayProducts);
$totalAll    = isset($pagination['total_items']) ? (int)$pagination['total_items'] : count($products ?? []);
$currentPage = (int)($pagination['current_page'] ?? 1);
$totalPages  = (int)($pagination['total_pages'] ?? 1);

/* URL builder ВІД КАТЕГОРІЇ */
$buildUrl = function(array $overrides = []) use ($categoryPath): string {
    $query = array_merge($_GET, $overrides);
    if (isset($query['sort']) && is_array($query['sort'])) {
        $query['sort'] = end($query['sort']);
    }
    return $categoryPath . '?' . http_build_query($query);
};
?>

<section class="mb-4">
    <!-- HERO -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body px-3 px-md-4 py-3 py-md-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb small mb-0">
                            <li class="breadcrumb-item">
                                <a href="/" class="text-decoration-none text-muted">
                                    <?= __('common.home'); ?>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="/products" class="text-decoration-none text-muted">
                                    <?= __('catalog.breadcrumb.list'); ?>
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?= htmlspecialchars((string)($category['name'] ?? 'Категорія'), ENT_QUOTES, 'UTF-8'); ?>
                            </li>
                        </ol>
                    </nav>

                    <h1 class="h4 h3-md mb-1 fw-semibold">
                        <?= htmlspecialchars((string)($category['name'] ?? 'Категорія'), ENT_QUOTES, 'UTF-8'); ?>
                    </h1>
                    <p class="text-muted mb-0">
                        <?= __('category.products.subtitle'); ?>
                    </p>
                </div>

                <div class="text-md-end">
                    <div class="d-inline-flex flex-column align-items-md-end gap-1">
                        <span class="badge rounded-pill text-bg-warning text-dark fw-semibold px-3 py-2">
                            <i class="bi bi-box-seam me-1"></i>
                            <?= __('category.products.badge.on_page'); ?>: <?= $totalOnPage; ?>
                        </span>
                        <span class="small text-muted">
                            <?= __('category.products.badge.total'); ?>:
                            <span class="fw-semibold"><?= $totalAll; ?></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LAYOUT -->
    <div class="row g-3 g-md-4">

        <!-- LEFT: FILTERS -->
        <div class="col-12 col-lg-3">
            <form
                id="filtersForm"
                class="card border-0 shadow-sm rounded-4 mb-3 mb-lg-0"
                method="get"
                action="<?= htmlspecialchars($categoryPath, ENT_QUOTES, 'UTF-8'); ?>"
            >
                <div class="card-body p-3 p-md-4">

                    <!-- Пошук -->
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">
                            <?= __('catalog.filters.search.label'); ?>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input
                                type="text"
                                class="form-control border-start-0"
                                name="q"
                                placeholder="<?= __('catalog.filters.search.placeholder'); ?>"
                                value="<?= htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8'); ?>"
                            >
                        </div>
                    </div>

                    <!-- Бренд -->
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">
                            <?= __('catalog.filters.brand.label'); ?>
                        </label>
                        <select
                            class="form-select form-select-sm"
                            name="brand"
                            <?= empty($brands) ? 'disabled' : ''; ?>
                        >
                            <option value="">
                                <?= empty($brands)
                                    ? __('catalog.filters.brand.unavailable')
                                    : __('catalog.filters.brand.all'); ?>
                            </option>
                            <?php foreach ($brands as $brand): ?>
                                <?php
                                $brandSlug = (string)($brand['slug'] ?? '');
                                $brandName = (string)($brand['name'] ?? '');
                                if ($brandSlug === '' || $brandName === '') continue;
                                ?>
                                <option
                                    value="<?= htmlspecialchars($brandSlug, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?= $currentBrand === $brandSlug ? 'selected' : ''; ?>
                                >
                                    <?= htmlspecialchars($brandName, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Явний фільтр категорії -->
                    <input
                        type="hidden"
                        name="category"
                        value="<?= htmlspecialchars($currentCat, ENT_QUOTES, 'UTF-8'); ?>"
                    >

                    <!-- Ціна -->
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1 d-flex justify-content-between">
                            <span><?= __('catalog.filters.price.label'); ?></span>
                            <span class="text-muted small">
                                <?= __('catalog.filters.price.range_hint'); ?>
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
                                    value="<?= htmlspecialchars($priceMin, ENT_QUOTES, 'UTF-8'); ?>"
                                >
                            </div>
                            <div class="col-6">
                                <input
                                    type="number"
                                    min="0"
                                    step="1"
                                    class="form-control form-control-sm"
                                    name="price_max"
                                    placeholder="5000"
                                    value="<?= htmlspecialchars($priceMax, ENT_QUOTES, 'UTF-8'); ?>"
                                >
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
                                <?= $inStock === '1' ? 'checked' : ''; ?>
                            >
                            <label class="form-check-label small" for="in_stock">
                                <i class="bi bi-check2-circle text-success me-1"></i>
                                <?= __('catalog.filters.in_stock.label'); ?>
                            </label>
                        </div>
                    </div>

                    <!-- К-сть на сторінці -->
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">
                            <?= __('catalog.filters.per_page.label'); ?>
                        </label>
                        <select class="form-select form-select-sm" name="per_page">
                            <?php foreach ([12, 24, 36, 48] as $n): ?>
                                <option value="<?= $n; ?>" <?= $perPage === $n ? 'selected' : ''; ?>>
                                    <?= $n; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Кнопки -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning text-dark fw-semibold">
                            <i class="bi bi-funnel me-1"></i>
                            <?= __('catalog.filters.apply'); ?>
                        </button>
                        <a
                            href="<?= htmlspecialchars($categoryPath, ENT_QUOTES, 'UTF-8'); ?>"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            <i class="bi bi-x-circle me-1"></i>
                            <?= __('catalog.filters.reset_all'); ?>
                        </a>
                    </div>

                </div>
            </form>
        </div>

        <!-- RIGHT -->
        <div class="col-12 col-lg-9">
            <!-- Панель сортування -->
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body py-2 px-3 px-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="small text-muted">
                            <?= __('catalog.sort.summary_prefix'); ?>
                            <span class="fw-semibold"><?= $totalOnPage; ?></span>
                            <?= __('catalog.sort.summary_of'); ?>
                            <span class="fw-semibold"><?= $totalAll; ?></span>
                            <?= __('catalog.sort.summary_suffix'); ?>
                            <?php if ($searchValue !== ''): ?>
                                <?= ' ' . __('catalog.search.query_label'); ?>
                                «<?= htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8'); ?>»
                            <?php endif; ?>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">
                                <?= __('catalog.sort.label'); ?>
                            </span>
                            <select
                                class="form-select form-select-sm"
                                name="sort"
                                form="filtersForm"
                                onchange="this.form && this.form.submit()"
                            >
                                <option value=""><?= __('catalog.sort.default'); ?></option>
                                <option value="price_asc"   <?= $currentSort === 'price_asc'   ? 'selected' : ''; ?>>
                                    <?= __('catalog.sort.price_asc'); ?>
                                </option>
                                <option value="price_desc"  <?= $currentSort === 'price_desc'  ? 'selected' : ''; ?>>
                                    <?= __('catalog.sort.price_desc'); ?>
                                </option>
                                <option value="name_asc"    <?= $currentSort === 'name_asc'    ? 'selected' : ''; ?>>
                                    <?= __('catalog.sort.name_asc'); ?>
                                </option>
                                <option value="name_desc"   <?= $currentSort === 'name_desc'   ? 'selected' : ''; ?>>
                                    <?= __('catalog.sort.name_desc'); ?>
                                </option>
                                <option value="newest"      <?= $currentSort === 'newest'      ? 'selected' : ''; ?>>
                                    <?= __('catalog.sort.newest'); ?>
                                </option>
                                <option value="popular"     <?= $currentSort === 'popular'     ? 'selected' : ''; ?>>
                                    <?= __('catalog.sort.popular'); ?>
                                </option>
                                <option value="discount"    <?= $currentSort === 'discount'    ? 'selected' : ''; ?>>
                                    <?= __('catalog.sort.discount'); ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRID -->
            <?php if (empty($displayProducts)): ?>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-cart-x fs-1 text-muted"></i>
                        </div>

                        <?php if ($minSet || $maxSet): ?>
                            <h2 class="h5 mb-2">
                                <?= __('catalog.empty.price_range.title'); ?>
                            </h2>
                            <p class="text-muted mb-3">
                                <?= __('catalog.empty.price_range.text_prefix'); ?>
                                <?php if ($minSet): ?>
                                    <?= ' ' . __('catalog.filters.price.label'); ?>
                                    <span class="fw-semibold">
                                        <?= htmlspecialchars($priceMin, ENT_QUOTES, 'UTF-8'); ?> ₴
                                    </span>
                                <?php endif; ?>
                                <?php if ($maxSet): ?>
                                    <?php if ($minSet): ?>
                                        <?= ' ' . __('catalog.filters.price.range_hint'); ?>
                                    <?php endif; ?>
                                    <span class="fw-semibold">
                                        <?= htmlspecialchars($priceMax, ENT_QUOTES, 'UTF-8'); ?> ₴
                                    </span>
                                <?php endif; ?>
                                <?= ' ' . __('catalog.empty.price_range.text_suffix'); ?>
                            </p>
                        <?php else: ?>
                            <h2 class="h5 mb-2">
                                <?= __('catalog.empty.filtered.title'); ?>
                            </h2>
                            <p class="text-muted mb-3">
                                <?= __('catalog.empty.filtered.text'); ?>
                            </p>
                        <?php endif; ?>

                        <a
                            href="<?= htmlspecialchars($categoryPath, ENT_QUOTES, 'UTF-8'); ?>"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            <?= __('catalog.filters.reset_all'); ?>
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

            <!-- PAGINATION -->
            <?php if ($totalPages > 1 && !empty($displayProducts)): ?>
                <nav aria-label="<?= __('pagination.aria_label'); ?>" class="mt-4">
                    <ul class="pagination justify-content-center mb-0">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a
                                    class="page-link"
                                    href="<?= htmlspecialchars($buildUrl(['page' => $currentPage - 1]), ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-label="<?= __('pagination.prev'); ?>"
                                >
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item<?= $i === $currentPage ? ' active' : ''; ?>">
                                <a
                                    class="page-link"
                                    href="<?= htmlspecialchars($buildUrl(['page' => $i]), ENT_QUOTES, 'UTF-8'); ?>"
                                >
                                    <?= $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a
                                    class="page-link"
                                    href="<?= htmlspecialchars($buildUrl(['page' => $currentPage + 1]), ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-label="<?= __('pagination.next'); ?>"
                                >
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
