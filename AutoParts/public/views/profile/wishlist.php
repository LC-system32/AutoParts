<?php
/** @var array<int, array<string, mixed>> $items */
/** @var string|null $flash */

$items = $items ?? [];
$count = count($items);
?>

<section class="py-4 py-md-5 bg-light-subtle">
    <div class="container-xxl">

        <!-- HERO-БЛОК -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 mb-md-5">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <div class="me-3">
                    <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center shadow-sm"
                         style="width:72px;height:72px;">
                        <i class="bi bi-heart fs-1 text-warning"></i>
                    </div>
                </div>
                <div>
                    <h1 class="fw-bold fs-3 mb-1">
                        <?= __('page.wishlist.title'); ?>
                    </h1>
                    <p class="text-muted mb-0">
                        <?= __('page.wishlist.hero.subtitle'); ?>
                    </p>
                </div>
            </div>

            <?php if (!empty($items)): ?>
                <div class="text-md-end">
                    <span class="badge bg-dark-subtle text-dark-emphasis px-3 py-2 rounded-pill">
                        <i class="bi bi-heart-fill text-warning me-1"></i>
                        <?= __('page.wishlist.badge.count', null, ['count' => $count]); ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-info alert-dismissible fade show mb-4 shadow-sm rounded-4" role="alert">
                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="<?= __('action.close', 'Закрити'); ?>"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($items)): ?>

            <!-- ПУСТИЙ СПИСОК БАЖАНЬ -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5 text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center shadow-sm"
                             style="width:80px;height:80px;">
                            <i class="bi bi-heartbreak fs-1 text-warning"></i>
                        </div>
                    </div>
                    <h2 class="h5 fw-semibold mb-2">
                        <?= __('page.wishlist.empty.title'); ?>
                    </h2>
                    <p class="text-muted mb-3 mb-md-4">
                        <?= __('page.wishlist.empty.text'); ?>
                    </p>
                    <a href="/products" class="btn btn-warning text-dark fw-semibold px-4">
                        <i class="bi bi-search me-1"></i>
                        <?= __('page.wishlist.empty.button'); ?>
                    </a>
                </div>
            </div>

        <?php else: ?>

            <!-- СПИСОК ТОВАРІВ У СПИСКУ БАЖАНЬ -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                <?php foreach ($items as $item): ?>
                    <?php $product = $item['product'] ?? $item; ?>
                    <?php
                        $name = htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8');
                        $slug = htmlspecialchars($product['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                        $img  = $product['image']
                            ?? ($product['images'][0]['url'] ?? null)
                            ?? '';
                        $imgSafe = $img ? htmlspecialchars($img, ENT_QUOTES, 'UTF-8') : '';
                        $price  = isset($product['price']) ? (float)$product['price'] : null;
                    ?>
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative wishlist-card">
                            <!-- маленьке сердечко-іконка у куті -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill shadow-sm">
                                    <i class="bi bi-heart-fill"></i>
                                </span>
                            </div>

                            <?php if ($imgSafe): ?>
                                <a href="/products/<?= $slug; ?>" class="d-block bg-white">
                                    <img
                                        src="<?= $imgSafe; ?>"
                                        class="card-img-top p-3"
                                        style="height:210px; object-fit:contain;"
                                        alt="<?= $name; ?>"
                                    >
                                </a>
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center p-4 bg-white">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                         style="width:80px;height:80px;">
                                        <i class="bi bi-gear fs-2 text-warning"></i>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column p-3">
                                <h2 class="h6 fw-semibold mb-1">
                                    <a href="/products/<?= $slug; ?>" class="text-decoration-none text-dark">
                                        <?= $name; ?>
                                    </a>
                                </h2>

                                <?php if (!empty($product['brand_name'])): ?>
                                    <div class="mb-2">
                                        <span class="badge bg-light text-muted border">
                                            <i class="bi bi-building me-1"></i>
                                            <?= htmlspecialchars($product['brand_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($price !== null): ?>
                                    <div class="mb-2">
                                        <span class="fw-bold fs-5 text-dark">
                                            <?= number_format($price, 2, '.', ' '); ?>
                                            <?= __('price.currency.uah', 'грн'); ?>
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <div class="mb-2 text-muted small">
                                        <i class="bi bi-info-circle me-1"></i>
                                        <?= __('page.wishlist.price_unknown'); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="mt-auto d-flex flex-wrap gap-2 pt-2 border-top">
                                    <!-- Видалити зі списку бажань -->
                                    <form action="/wishlist/remove" method="post" class="d-inline-block">
                                        <?= \App\Core\Csrf::csrfInput(); ?>
                                        <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 0); ?>">
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger d-flex align-items-center">
                                            <i class="bi bi-trash me-1"></i>
                                            <span><?= __('page.wishlist.button.remove'); ?></span>
                                        </button>
                                    </form>

                                    <!-- Додати до кошика -->
                                    <form action="/cart/add" method="post" class="d-inline-block">
                                        <?= \App\Core\Csrf::csrfInput(); ?>
                                        <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 0); ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit"
                                                class="btn btn-sm btn-warning text-dark fw-semibold d-flex align-items-center">
                                            <i class="bi bi-cart-plus me-1"></i>
                                            <span><?= __('page.wishlist.button.to_cart'); ?></span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</section>

<style>
    /* легкий hover-ефект тільки для цієї сторінки */
    .wishlist-card {
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .wishlist-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08);
    }
</style>
