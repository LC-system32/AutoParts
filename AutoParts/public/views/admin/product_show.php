<?php
/**
 * @var string|null                  $flash
 * @var array<string,mixed>          $product
 * @var array<int,array<string,mixed>> $offers
 */

$flash   = $flash   ?? null;
$product = $product ?? [];
$offers  = $offers  ?? [];

$section = 'products';

$id           = (int)($product['id'] ?? 0);
$name         = (string)($product['name'] ?? 'Без назви');
$sku          = (string)($product['sku'] ?? '');
$brandName    = (string)($product['brand_name'] ?? '—');
$categoryName = (string)($product['category_name'] ?? '—');
$isActive     = !empty($product['is_active']);
$slug         = (string)($product['slug'] ?? '');
$shortDesc    = (string)($product['short_description'] ?? '');
$fullDesc     = (string)($product['description'] ?? ($product['full_description'] ?? ''));
$createdAt    = (string)($product['created_at'] ?? '');
$updatedAt    = (string)($product['updated_at'] ?? '');
?>
<section class="py-3 py-md-4">
    <div class="container-fluid">
        <div class="row">
            <?php include '_sidebar.php'; ?>

            <div class="col-12 col-lg-9 col-xl-10">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body p-3 p-md-4">

                        <!-- Заголовок + дії -->
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center me-3"
                                     style="width:56px;height:56px;">
                                    <i class="bi bi-box-seam fs-3 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.products.show.heading'); ?>
                                        #<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.products.show.subtitle'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2">
                                <a href="/admin/products/<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>/edit"
                                   class="btn btn-primary btn-sm fw-semibold">
                                    <i class="bi bi-pencil-square me-1"></i>
                                    <?= __('admin.products.show.edit_button'); ?>
                                </a>
                                <a href="/admin/products"
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    <?= __('admin.products.show.back_button'); ?>
                                </a>
                            </div>
                        </div>

                        <?php if (!empty($flash)): ?>
                            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                                <button type="button"
                                        class="btn-close"
                                        data-bs-dismiss="alert"
                                        aria-label="<?= __('common.close'); ?>"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Основна інформація -->
                        <div class="row g-3 g-md-4">
                            <div class="col-12 col-lg-7">
                                <div class="border rounded-4 p-3 p-md-4 h-100">
                                    <h2 class="h5 fw-semibold mb-3">
                                        <?= __('admin.products.show.main.title'); ?>
                                    </h2>

                                    <div class="mb-2">
                                        <div class="text-muted small mb-1">
                                            <?= __('admin.products.show.main.name'); ?>
                                        </div>
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    </div>

                                    <div class="mb-2 d-flex flex-wrap small">
                                        <div class="me-3 mb-1">
                                            <span class="text-muted">
                                                <?= __('admin.products.show.main.id'); ?>:
                                            </span>
                                            <span class="fw-semibold">
                                                <?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </div>
                                        <?php if ($sku !== ''): ?>
                                            <div class="me-3 mb-1">
                                                <span class="text-muted">
                                                    <?= __('admin.products.show.main.sku'); ?>:
                                                </span>
                                                <span class="fw-semibold">
                                                    <?= htmlspecialchars($sku, ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($slug !== ''): ?>
                                            <div class="mb-1">
                                                <span class="text-muted">
                                                    <?= __('admin.products.show.main.slug'); ?>:
                                                </span>
                                                <span class="fw-semibold">
                                                    <?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-2 small">
                                        <div class="text-muted mb-1">
                                            <?= __('admin.products.show.main.brand_category'); ?>
                                        </div>
                                        <div>
                                            <i class="bi bi-tag me-1 text-warning"></i>
                                            <?= htmlspecialchars($brandName, ENT_QUOTES, 'UTF-8'); ?>
                                            <span class="text-muted mx-1">·</span>
                                            <i class="bi bi-diagram-3 me-1 text-warning"></i>
                                            <?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="text-muted small mb-1">
                                            <?= __('admin.products.show.main.status.title'); ?>
                                        </div>
                                        <?php if ($isActive): ?>
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="bi bi-check-circle me-1"></i>
                                                <?= __('admin.products.show.main.status.active'); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                <i class="bi bi-eye-slash me-1"></i>
                                                <?= __('admin.products.show.main.status.inactive'); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($shortDesc !== ''): ?>
                                        <div class="mb-3">
                                            <div class="text-muted small mb-1">
                                                <?= __('admin.products.show.main.short_description'); ?>
                                            </div>
                                            <div>
                                                <?= nl2br(htmlspecialchars($shortDesc, ENT_QUOTES, 'UTF-8')); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($fullDesc !== ''): ?>
                                        <div class="mb-3">
                                            <div class="text-muted small mb-1">
                                                <?= __('admin.products.show.main.full_description'); ?>
                                            </div>
                                            <div class="small">
                                                <?= nl2br(htmlspecialchars($fullDesc, ENT_QUOTES, 'UTF-8')); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="small text-muted">
                                        <div class="mb-1">
                                            <i class="bi bi-clock-history me-1"></i>
                                            <?= __('admin.products.show.main.created_at'); ?>
                                            <?= htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                        <?php if ($updatedAt !== ''): ?>
                                            <div>
                                                <i class="bi bi-arrow-repeat me-1"></i>
                                                <?= __('admin.products.show.main.updated_at'); ?>
                                                <?= htmlspecialchars($updatedAt, ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Пропозиції / ціни -->
                            <div class="col-12 col-lg-5">
                                <div class="border rounded-4 p-3 p-md-4 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h2 class="h6 fw-semibold mb-0">
                                            <?= __('admin.products.show.offers.title'); ?>
                                        </h2>
                                        <span class="badge bg-light text-muted small">
                                            <?= count($offers); ?>
                                            <?= __('admin.products.show.offers.badge'); ?>
                                        </span>
                                    </div>

                                    <?php if (empty($offers)): ?>
                                        <div class="text-muted small">
                                            <?= __('admin.products.show.offers.empty'); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0 small">
                                                <thead class="table-light">
                                                <tr>
                                                    <th><?= __('admin.products.show.offers.table.id'); ?></th>
                                                    <th><?= __('admin.products.show.offers.table.warehouse'); ?></th>
                                                    <th class="text-end"><?= __('admin.products.show.offers.table.qty'); ?></th>
                                                    <th class="text-end"><?= __('admin.products.show.offers.table.price'); ?></th>
                                                    <th><?= __('admin.products.show.offers.table.status'); ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($offers as $offer): ?>
                                                    <?php
                                                    $offerId   = (int)($offer['id'] ?? 0);
                                                    $whName    = (string)($offer['warehouse_name'] ?? $offer['warehouse'] ?? '—');
                                                    $qty       = (float)($offer['quantity'] ?? 0);
                                                    $price     = (float)($offer['sale_price'] ?? $offer['price'] ?? 0);
                                                    $currency  = (string)($offer['currency'] ?? 'UAH');
                                                    $isActiveO = !empty($offer['is_active']);
                                                    ?>
                                                    <tr>
                                                        <td><?= $offerId; ?></td>
                                                        <td><?= htmlspecialchars($whName, ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end"><?= $qty; ?></td>
                                                        <td class="text-end">
                                                            <?= number_format($price, 2, '.', ' '); ?>
                                                            <?= htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?= $isActiveO ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>">
                                                                <?= $isActiveO
                                                                    ? __('admin.products.show.offers.status.active')
                                                                    : __('admin.products.show.offers.status.disabled'); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="small text-muted mt-2">
                                            <?= __('admin.products.show.offers.note'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div><!-- /row -->

                    </div>
                </div>
            </div><!-- /col main -->
        </div>
    </div>
</section>
