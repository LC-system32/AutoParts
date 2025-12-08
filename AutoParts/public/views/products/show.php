<?php

/** @var array<string, mixed> $product */
/** @var array<int, array<string, mixed>> $reviews */

$images    = $product['images'] ?? [];
$mainImg   = $product['image'] ?? ($images[0]['url'] ?? null) ?? '';
$inStock   = $product['in_stock'] ?? null;
$price     = $product['price'] ?? null;
$basePrice = $product['base_price'] ?? null;
$sku       = $product['sku'] ?? null;
$oem       = $product['oem_number'] ?? null;
$weight    = $product['weight_kg'] ?? null;
$isPopular = !empty($product['is_popular']);

$brand     = $product['brand']     ?? null;
$category  = $product['category']  ?? null;

// Додаткові дані
$attributes = isset($product['attributes']) && is_array($product['attributes'])
    ? $product['attributes']
    : [];
$oilSpecs = isset($product['oil_specs']) && is_array($product['oil_specs'])
    ? $product['oil_specs']
    : null;

// Сумісність
$fitments = [];
if (isset($product['fitments']) && is_array($product['fitments'])) {
    $fitments = $product['fitments'];
}

// Відгуки
$reviewsCount = count($reviews);
$avgRating    = null;
if ($reviewsCount > 0) {
    $sum = 0;
    $cnt = 0;
    foreach ($reviews as $r) {
        if (!empty($r['rating'])) {
            $sum += (int)$r['rating'];
            $cnt++;
        }
    }
    if ($cnt > 0) {
        $avgRating = round($sum / $cnt, 1);
    }
}
?>

<section class="mb-5">

    <!-- Хлібні крихти -->
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item">
                    <a href="/" class="text-decoration-none">
                        <?= __('nav.home', 'Головна'); ?>
                    </a>
                </li>
                <?php if (!empty($category['slug'])): ?>
                    <li class="breadcrumb-item">
                        <a href="/categories/<?= htmlspecialchars($category['slug'], ENT_QUOTES, 'UTF-8'); ?>"
                            class="text-decoration-none">
                            <?= htmlspecialchars(
                                $category['name'] ?? __('product.breadcrumb.category_fallback', 'Категорія'),
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
                </li>
            </ol>
        </nav>
    </div>

    <div class="row g-4">

        <!-- Ліва колонка: зображення + галерея -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white position-relative mb-3">
                <?php if ($isPopular): ?>
                    <span class="position-absolute top-0 start-0 m-2 badge rounded-pill bg-warning text-dark">
                        <i class="bi bi-star-fill me-1"></i> <?= __('product.badge.popular'); ?>
                    </span>
                <?php endif; ?>

                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-center mb-3"
                        style="min-height: 260px;">
                        <?php if ($mainImg): ?>
                            <img
                                src="<?= htmlspecialchars($mainImg, ENT_QUOTES, 'UTF-8'); ?>"
                                class="img-fluid"
                                alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                style="max-height: 320px; object-fit: contain;">
                        <?php else: ?>
                            <div class="text-muted text-center">
                                <i class="bi bi-image fs-1 d-block mb-2"></i>
                                <span class="small">
                                    <?= __('product.image.missing'); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (count($images) > 1): ?>
                        <div class="d-flex flex-nowrap gap-2 overflow-auto pb-1">
                            <?php foreach ($images as $img): ?>
                                <div class="border rounded-3 bg-white p-1 flex-shrink-0">
                                    <img
                                        src="<?= htmlspecialchars($img['url'], ENT_QUOTES, 'UTF-8'); ?>"
                                        alt=""
                                        class="img-thumbnail border-0"
                                        style="width: 70px; height: 70px; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Коротка технічна інформація -->
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-3 p-md-4">
                    <h2 class="h6 mb-3"><?= __('product.info.title'); ?></h2>
                    <dl class="row small mb-0">
                        <?php if ($brand): ?>
                            <dt class="col-5 text-muted"><?= __('product.info.brand'); ?></dt>
                            <dd class="col-7">
                                <a href="/brands/<?= htmlspecialchars($brand['slug'], ENT_QUOTES, 'UTF-8'); ?>"
                                    class="text-decoration-none fw-semibold">
                                    <?= htmlspecialchars($brand['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </dd>
                        <?php endif; ?>

                        <?php if ($category): ?>
                            <dt class="col-5 text-muted"><?= __('product.info.category'); ?></dt>
                            <dd class="col-7">
                                <a href="/categories/<?= htmlspecialchars($category['slug'], ENT_QUOTES, 'UTF-8'); ?>"
                                    class="text-decoration-none">
                                    <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </dd>
                        <?php endif; ?>

                        <?php if ($sku): ?>
                            <dt class="col-5 text-muted"><?= __('product.info.sku'); ?></dt>
                            <dd class="col-7 fw-semibold"><?= htmlspecialchars($sku, ENT_QUOTES, 'UTF-8'); ?></dd>
                        <?php endif; ?>

                        <?php if ($oem): ?>
                            <dt class="col-5 text-muted"><?= __('product.info.oem'); ?></dt>
                            <dd class="col-7 fw-semibold"><?= htmlspecialchars($oem, ENT_QUOTES, 'UTF-8'); ?></dd>
                        <?php endif; ?>

                        <?php if ($weight): ?>
                            <dt class="col-5 text-muted"><?= __('product.info.weight'); ?></dt>
                            <dd class="col-7">
                                <?= htmlspecialchars($weight, ENT_QUOTES, 'UTF-8'); ?>
                                <?= __('product.unit.kg', 'кг'); ?>
                            </dd>
                        <?php endif; ?>

                        <?php if ($oilSpecs && (!empty($oilSpecs['viscosity']) || !empty($oilSpecs['volume_l']) || !empty($oilSpecs['oil_type']))): ?>
                            <dt class="col-5 text-muted"><?= __('product.info.viscosity'); ?></dt>
                            <dd class="col-7">
                                <?= htmlspecialchars($oilSpecs['viscosity'] ?? '—', ENT_QUOTES, 'UTF-8'); ?>
                            </dd>
                            <dt class="col-5 text-muted"><?= __('product.info.volume'); ?></dt>
                            <dd class="col-7">
                                <?php if (!empty($oilSpecs['volume_l'])): ?>
                                    <?= htmlspecialchars($oilSpecs['volume_l'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?= __('product.unit.l', 'л'); ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </dd>
                            <dt class="col-5 text-muted"><?= __('product.info.oil_type'); ?></dt>
                            <dd class="col-7">
                                <?= htmlspecialchars($oilSpecs['oil_type'] ?? '—', ENT_QUOTES, 'UTF-8'); ?>
                            </dd>
                        <?php endif; ?>

                        <?php if (!empty($fitments)): ?>
                            <dt class="col-5 text-muted"><?= __('product.info.compatibility'); ?></dt>
                            <dd class="col-7">
                                <ul class="list-unstyled mb-0 small">
                                    <?php foreach ($fitments as $fit): ?>
                                        <?php
                                        $make  = $fit['make']  ?? ($fit['make_name']  ?? '');
                                        $model = $fit['model'] ?? ($fit['model_name'] ?? '');
                                        $gen   = $fit['generation'] ?? ($fit['generation_name'] ?? '');
                                        $mod   = $fit['modification'] ?? ($fit['modification_name'] ?? '');
                                        $years = $fit['years'] ?? ($fit['year_range'] ?? '');
                                        $engine = $fit['engine'] ?? ($fit['engine_name'] ?? '');
                                        $parts = [];
                                        if ($make !== '') {
                                            $parts[] = $make;
                                        }
                                        if ($model !== '') {
                                            $parts[] = $model;
                                        }
                                        if ($gen !== '') {
                                            $parts[] = $gen;
                                        }
                                        if ($mod !== '') {
                                            $parts[] = $mod;
                                        }
                                        $line = implode(' ', $parts);
                                        if ($years !== '') {
                                            $line .= ' (' . $years . ')';
                                        }
                                        if ($engine !== '') {
                                            $line .= ', ' . $engine;
                                        }
                                        ?>
                                        <li><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </dd>
                        <?php endif; ?>

                        <?php if (isset($inStock)): ?>
                            <dt class="col-5 text-muted"><?= __('product.info.availability'); ?></dt>
                            <dd class="col-7">
                                <?php if ($inStock): ?>
                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">
                                        <i class="bi bi-check2-circle me-1"></i>
                                        <?= __('product.info.in_stock'); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">
                                        <i class="bi bi-x-circle me-1"></i>
                                        <?= __('product.info.out_of_stock'); ?>
                                    </span>
                                <?php endif; ?>
                            </dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <?php if (!empty($offers)): ?>
                <div class="card border-0 shadow-sm rounded-4 bg-white mt-3">
                    <div class="card-body p-3 p-md-4">
                        <h2 class="h6 mb-3"><?= __('product.offers.title'); ?></h2>
                        <div class="mb-2 small">
                            <span class="me-2"><?= __('product.offers.sort.label'); ?></span>
                            <?php
                            $productSlug = $product['slug'] ?? '';
                            $sortOptions = [
                                'cheapest' => __('product.offers.sort.cheapest'),
                                'fastest'  => __('product.offers.sort.fastest'),
                                'city'     => __('product.offers.sort.city'),
                            ];
                            foreach ($sortOptions as $key => $label):
                                $url = '/products/' . urlencode((string)$productSlug) . '?offers_sort=' . $key;
                                $active = (isset($offersSort) && $offersSort === $key);
                            ?>
                                <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>"
                                    class="<?= $active ? 'fw-semibold text-decoration-underline' : 'text-decoration-none'; ?> me-2">
                                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th><?= __('product.offers.th.warehouse'); ?></th>
                                        <th><?= __('product.offers.th.supplier'); ?></th>
                                        <th class="text-end"><?= __('product.offers.th.quantity'); ?></th>
                                        <th class="text-end"><?= __('product.offers.th.price'); ?></th>
                                        <th class="text-end"><?= __('product.offers.th.term'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($offers as $of): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($of['warehouse'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                <?php if (!empty($of['city'])): ?>
                                                    <small class="text-muted">
                                                        (<?= htmlspecialchars($of['city'], ENT_QUOTES, 'UTF-8'); ?>)
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($of['supplier'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="text-end">
                                                <?= htmlspecialchars($of['quantity'], ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td class="text-end">
                                                <?php
                                                $sale = isset($of['sale_price']) ? (float)$of['sale_price'] : null;
                                                $base = isset($of['base_price']) ? (float)$of['base_price'] : null;
                                                if ($sale !== null):
                                                    echo number_format($sale, 2, '.', ' ') . ' ' . __('price.currency.uah', 'грн');
                                                    if ($base !== null && $base > $sale):
                                                        echo '<small class="text-muted text-decoration-line-through ms-1">'
                                                            . number_format($base, 2, '.', ' ')
                                                            . ' ' . __('price.currency.uah', 'грн')
                                                            . '</small>';
                                                    endif;
                                                else:
                                                    echo '—';
                                                endif;
                                                ?>
                                            </td>
                                            <td class="text-end">
                                                <?php if (!empty($of['delivery_days'])): ?>
                                                    <?= htmlspecialchars($of['delivery_days'], ENT_QUOTES, 'UTF-8'); ?>
                                                    <?= __('product.offers.days_suffix', 'дн.'); ?>
                                                <?php else: ?>
                                                    —
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Права колонка: назва, рейтинг, ціна, таби -->
        <div class="col-lg-7">

            <!-- Назва + рейтинг + артикул -->
            <div class="mb-3">
                <h1 class="h4 mb-2">
                    <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
                </h1>

                <div class="d-flex flex-wrap align-items-center gap-3 small">
                    <?php if ($avgRating !== null): ?>
                        <div class="d-inline-flex align-items-center gap-2">
                            <span class="text-warning">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="bi<?= $i < floor($avgRating) ? ' bi-star-fill' : ' bi-star'; ?>"></i>
                                <?php endfor; ?>
                            </span>
                            <span class="text-muted">
                                <?= __('product.rating.summary', null, [
                                    'rating' => (string)$avgRating,
                                    'count'  => (string)$reviewsCount,
                                ]); ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <span class="text-muted">
                            <?= __('product.rating.none'); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($sku): ?>
                        <span class="text-muted">
                            · <?= __('product.code.label'); ?>
                            <span class="fw-semibold"><?= htmlspecialchars($sku, ENT_QUOTES, 'UTF-8'); ?></span>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ціна + кнопки покупки -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-3">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-wrap align-items-baseline justify-content-between gap-3 mb-3">
                        <div>
                            <?php if ($price !== null): ?>
                                <div class="fw-bold fs-3 text-warning mb-1">
                                    <?= number_format((float)$price, 2, '.', ' '); ?>
                                    <?= __('price.currency.uah', 'грн'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($basePrice !== null && $basePrice > $price): ?>
                                <div class="small text-muted text-decoration-line-through">
                                    <?= number_format((float)$basePrice, 2, '.', ' '); ?>
                                    <?= __('price.currency.uah', 'грн'); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <form action="/cart/add" method="post" class="d-inline-block">
                                <?= \App\Core\Csrf::csrfInput(); ?>
                                <input type="hidden" name="product_id" value="<?= (int)$product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit"
                                    class="btn btn-warning text-dark fw-semibold d-inline-flex align-items-center"
                                    <?= (isset($inStock) && !$inStock) ? 'disabled' : ''; ?>>
                                    <i class="bi bi-cart-plus me-1"></i>
                                    <?= (isset($inStock) && !$inStock)
                                        ? __('product.button.out_of_stock')
                                        : __('product.button.add_to_cart'); ?>
                                </button>
                            </form>

                            <?php if (!empty($_SESSION['user'])): ?>
                                <form action="/wishlist/add" method="post" class="d-inline-block">
                                    <?= \App\Core\Csrf::csrfInput(); ?>
                                    <input type="hidden" name="product_id" value="<?= (int)$product['id']; ?>">
                                    <button type="submit"
                                        class="btn btn-outline-secondary d-inline-flex align-items-center">
                                        <i class="bi bi-heart me-1"></i>
                                        <?= __('product.button.add_to_wishlist'); ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Плюшки сервісу -->
                    <div class="row row-cols-1 row-cols-md-3 g-3 small">
                        <div class="col">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-truck text-warning fs-5"></i>
                                <div>
                                    <div class="fw-semibold"><?= __('product.service.delivery.title'); ?></div>
                                    <div class="text-muted"><?= __('product.service.delivery.text'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-credit-card text-warning fs-5"></i>
                                <div>
                                    <div class="fw-semibold"><?= __('product.service.payment.title'); ?></div>
                                    <div class="text-muted"><?= __('product.service.payment.text'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-shield-check text-warning fs-5"></i>
                                <div>
                                    <div class="fw-semibold"><?= __('product.service.warranty.title'); ?></div>
                                    <div class="text-muted"><?= __('product.service.warranty.text'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Короткий текст під ціною -->
            <?php if (!empty($product['short_desc'])): ?>
                <div class="mb-3">
                    <h2 class="h6 mb-2"><?= __('product.short.title'); ?></h2>
                    <p class="small text-muted mb-0">
                        <?= nl2br(htmlspecialchars($product['short_desc'], ENT_QUOTES, 'UTF-8')); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Таби: Характеристики / Опис / Відгуки -->
            <ul class="nav nav-tabs small mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active"
                        id="tab-specs"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-pane-specs"
                        type="button"
                        role="tab"
                        aria-controls="tab-pane-specs"
                        aria-selected="true">
                        <?= __('product.tabs.specs'); ?>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                        id="tab-description"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-pane-description"
                        type="button"
                        role="tab"
                        aria-controls="tab-pane-description"
                        aria-selected="false">
                        <?= __('product.tabs.description'); ?>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                        id="tab-reviews"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-pane-reviews"
                        type="button"
                        role="tab"
                        aria-controls="tab-pane-reviews"
                        aria-selected="false">
                        <?= __('product.tabs.reviews_with_count', null, ['count' => $reviewsCount]); ?>
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                <!-- TAB: Характеристики -->
                <div class="tab-pane fade show active" id="tab-pane-specs" role="tabpanel"
                    aria-labelledby="tab-specs" tabindex="0">
                    <div class="card border-0 shadow-sm rounded-4 bg-white mb-3">
                        <div class="card-body p-3 p-md-4">
                            <?php if (!empty($attributes)): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <tbody class="small">
                                            <?php foreach ($attributes as $attr): ?>
                                                <tr>
                                                    <td class="text-muted" style="width: 45%;">
                                                        <?= htmlspecialchars($attr['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                    </td>
                                                    <td class="fw-semibold">
                                                        <?= htmlspecialchars($attr['value'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                        <?php if (!empty($attr['unit'])): ?>
                                                            <span class="text-muted">
                                                                <?= htmlspecialchars($attr['unit'], ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <tbody class="small">
                                            <?php if ($brand): ?>
                                                <tr>
                                                    <td class="text-muted" style="width:45%;">
                                                        <?= __('product.info.brand'); ?>
                                                    </td>
                                                    <td class="fw-semibold">
                                                        <?= htmlspecialchars($brand['name'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if ($category): ?>
                                                <tr>
                                                    <td class="text-muted">
                                                        <?= __('product.info.category'); ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if ($sku): ?>
                                                <tr>
                                                    <td class="text-muted">
                                                        <?= __('product.info.sku'); ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($sku, ENT_QUOTES, 'UTF-8'); ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if ($oem): ?>
                                                <tr>
                                                    <td class="text-muted">
                                                        <?= __('product.info.oem'); ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($oem, ENT_QUOTES, 'UTF-8'); ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if ($weight): ?>
                                                <tr>
                                                    <td class="text-muted">
                                                        <?= __('product.info.weight'); ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($weight, ENT_QUOTES, 'UTF-8'); ?>
                                                        <?= __('product.unit.kg', 'кг'); ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>

                                            <?php if ($oilSpecs): ?>
                                                <?php if (!empty($oilSpecs['viscosity'])): ?>
                                                    <tr>
                                                        <td class="text-muted">
                                                            <?= __('product.info.viscosity'); ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($oilSpecs['viscosity'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if (!empty($oilSpecs['volume_l'])): ?>
                                                    <tr>
                                                        <td class="text-muted">
                                                            <?= __('product.info.volume'); ?>
                                                        </td>
                                                        <td>
                                                            <?= htmlspecialchars($oilSpecs['volume_l'], ENT_QUOTES, 'UTF-8'); ?>
                                                            <?= __('product.unit.l', 'л'); ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if (!empty($oilSpecs['oil_type'])): ?>
                                                    <tr>
                                                        <td class="text-muted">
                                                            <?= __('product.info.oil_type'); ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($oilSpecs['oil_type'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- TAB: Опис -->
                <div class="tab-pane fade" id="tab-pane-description" role="tabpanel"
                    aria-labelledby="tab-description" tabindex="0">
                    <div class="card border-0 shadow-sm rounded-4 bg-white mb-3">
                        <div class="card-body p-3 p-md-4">
                            <?php if (!empty($product['description'])): ?>
                                <p class="text-muted small mb-0" style="white-space: pre-line;">
                                    <?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            <?php else: ?>
                                <p class="text-muted small mb-0">
                                    <?= __('product.description.empty'); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- TAB: Відгуки -->
                <div class="tab-pane fade" id="tab-pane-reviews" role="tabpanel"
                    aria-labelledby="tab-reviews" tabindex="0">
                    <div class="pt-3" id="reviews">

                        <?php if (!empty($reviews)): ?>
                            <div class="vstack gap-3 mb-3">
                                <?php foreach ($reviews as $review): ?>
                                    <div class="card border-0 shadow-sm rounded-4 bg-white">
                                        <div class="card-body p-3 p-md-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <strong>
                                                        <?= htmlspecialchars(
                                                            $review['user_name'] ?? __('product.reviews.user_anonymous'),
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ); ?>
                                                    </strong>
                                                    <?php if (!empty($review['rating'])): ?>
                                                        <div class="text-warning small">
                                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                                <i class="bi<?= $i < $review['rating'] ? ' bi-star-fill' : ' bi-star'; ?>"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if (!empty($review['created_at'])): ?>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars(date('d.m.Y', strtotime($review['created_at'])), ENT_QUOTES, 'UTF-8'); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>

                                            <p class="mb-0 small">
                                                <?= nl2br(htmlspecialchars($review['comment'] ?? $review['body'] ?? '', ENT_QUOTES, 'UTF-8')); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="card border-0 shadow-sm rounded-4 bg-white mb-3">
                                <div class="card-body text-center py-4">
                                    <div class="mb-2">
                                        <i class="bi bi-chat-left-text fs-3 text-muted"></i>
                                    </div>
                                    <h3 class="h6 mb-1">
                                        <?= __('product.reviews.none.title'); ?>
                                    </h3>
                                    <p class="text-muted small mb-0">
                                        <?= __('product.reviews.none.text'); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['user'])): ?>
                            <div class="card border-0 shadow-sm rounded-4 bg-white">
                                <div class="card-body p-3 p-md-4">
                                    <h3 class="h6 mb-3">
                                        <?= __('product.reviews.form.title'); ?>
                                    </h3>
                                    <form action="/products/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8'); ?>/reviews"
                                        method="post" class="row g-3">
                                        <?= \App\Core\Csrf::csrfInput(); ?>
                                        <input type="hidden" name="product_id" value="<?= (int)$product['id']; ?>">

                                        <div class="col-12 col-md-3">
                                            <label for="rating" class="form-label small mb-1">
                                                <?= __('product.reviews.form.rating_label'); ?>
                                            </label>
                                            <select name="rating" id="rating" class="form-select form-select-sm" required>
                                                <option value="5"><?= __('product.reviews.rating.5'); ?></option>
                                                <option value="4"><?= __('product.reviews.rating.4'); ?></option>
                                                <option value="3"><?= __('product.reviews.rating.3'); ?></option>
                                                <option value="2"><?= __('product.reviews.rating.2'); ?></option>
                                                <option value="1"><?= __('product.reviews.rating.1'); ?></option>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label for="comment" class="form-label small mb-1">
                                                <?= __('product.reviews.form.comment_label'); ?>
                                            </label>
                                            <textarea
                                                name="comment"
                                                id="comment"
                                                rows="4"
                                                class="form-control"
                                                required
                                                placeholder="<?= __('product.reviews.form.placeholder'); ?>"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-warning text-dark fw-semibold">
                                                <?= __('product.reviews.form.submit'); ?>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>