<?php
/** @var array<string,mixed> $product */
$img       = $product['image'] ?? ($product['images'][0]['url'] ?? '') ?? '';
$brandName = $product['brand']['name'] ?? null;
$price     = $product['price'] ?? null;
$inStock   = $product['in_stock'] ?? null; // true/false, якщо є
$isPopular = !empty($product['is_popular']);
?>
<div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative bg-white">

    <?php if ($isPopular): ?>
        <span class="position-absolute top-0 start-0 m-2 badge rounded-pill bg-warning text-dark">
            <i class="bi bi-star-fill me-1"></i> <?= __('product.badge.popular'); ?>
        </span>
    <?php endif; ?>

    <a href="/products/<?= htmlspecialchars($product['slug']); ?>" class="text-decoration-none">
        <div class="bg-body-tertiary d-flex align-items-center justify-content-center px-3 pt-3 pb-2" style="height: 200px;">
            <?php if ($img): ?>
                <img
                    src="<?= htmlspecialchars($img); ?>"
                    class="img-fluid"
                    alt="<?= htmlspecialchars($product['name']); ?>"
                    style="max-height: 100%; object-fit: contain;"
                >
            <?php else: ?>
                <div class="text-muted d-flex flex-column align-items-center justify-content-center w-100 h-100">
                    <i class="bi bi-image fs-2 mb-1"></i>
                    <small><?= __('product.image_missing'); ?></small>
                </div>
            <?php endif; ?>
        </div>
    </a>

    <div class="card-body d-flex flex-column p-3">

        <?php if ($brandName): ?>
            <div class="mb-1">
                <span class="badge rounded-pill bg-light text-secondary text-uppercase small">
                    <?= htmlspecialchars($brandName); ?>
                </span>
            </div>
        <?php endif; ?>

        <h6 class="card-title mb-1">
            <a href="/products/<?= htmlspecialchars($product['slug']); ?>"
               class="text-decoration-none text-dark stretched-link">
                <?= htmlspecialchars($product['name']); ?>
            </a>
        </h6>

        <?php if (!empty($product['short_desc'])): ?>
            <p class="text-muted small mb-2">
                <?= htmlspecialchars($product['short_desc']); ?>
            </p>
        <?php endif; ?>

        <?php if (isset($inStock)): ?>
            <span class="small mb-2 d-inline-flex align-items-center">
                <?php if ($inStock): ?>
                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle me-1">
                        <i class="bi bi-check2-circle me-1"></i> <?= __('product.in_stock'); ?>
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle me-1">
                        <i class="bi bi-x-circle me-1"></i> <?= __('product.out_of_stock'); ?>
                    </span>
                <?php endif; ?>
            </span>
        <?php endif; ?>

        <div class="mt-auto pt-2">
            <?php if ($price !== null): ?>
                <div class="d-flex align-items-baseline justify-content-between mb-2">
                    <div>
                        <div class="fw-bold fs-6 text-warning">
                            <?= number_format((float)$price, 2, '.', ' '); ?>
                            <?= __('price.currency.uah', 'грн'); ?>
                        </div>
                        <?php if (!empty($product['base_price']) && $product['base_price'] > $price): ?>
                            <div class="small text-muted text-decoration-line-through">
                                <?= number_format((float)$product['base_price'], 2, '.', ' '); ?>
                                <?= __('price.currency.uah', 'грн'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="/cart/add" method="post" class="d-grid">
                <?= \App\Core\Csrf::csrfInput(); ?>
                <input type="hidden" name="product_id" value="<?= (int)$product['id']; ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                        class="btn btn-sm btn-warning text-dark fw-semibold d-inline-flex align-items-center justify-content-center"
                    <?= (isset($inStock) && !$inStock) ? 'disabled' : ''; ?>>
                    <i class="bi bi-cart-plus me-1"></i>
                    <?= (isset($inStock) && !$inStock)
                        ? __('product.out_of_stock')
                        : __('product.to_cart'); ?>
                </button>
            </form>
        </div>
    </div>
</div>
