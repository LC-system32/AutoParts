<?php
/**
 * Спільна ліва навігація адмінки.
 *
 * @var string $section  поточний розділ (dashboard, users, orders, products, categories, brands, stock, discounts, banners, reviews, support, pages, faq, logs, settings)
 */
$section = $section ?? '';
?>
<aside class="col-12 col-lg-3 col-xl-2 mb-3 mb-lg-0 h-100">
    <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body p-3 p-md-3">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center me-2"
                     style="width:40px;height:40px;">
                    <i class="bi bi-tools fs-5 text-dark"></i>
                </div>
                <div>
                    <div class="small text-muted text-uppercase">
                        <?= __('admin.sidebar.panel_subtitle'); ?>
                    </div>
                    <div class="fw-semibold">
                        <?= __('admin.sidebar.panel_title'); ?>
                    </div>
                </div>
            </div>

            <nav class="nav flex-column small">
                <span class="text-uppercase text-muted fw-semibold mb-2 mt-1">
                    <?= __('admin.sidebar.group.overview'); ?>
                </span>
                <a href="/admin"
                   class="nav-link px-0 py-1 d-flex align-items-center<?= $section === 'dashboard' ? ' active fw-semibold text-dark' : ''; ?>">
                    <i class="bi bi-speedometer2 me-2"></i>
                    <?= __('admin.sidebar.link.dashboard'); ?>
                </a>

                <span class="text-uppercase text-muted fw-semibold mb-2 mt-3">
                    <?= __('admin.sidebar.group.management'); ?>
                </span>
                <a href="/admin/users"
                   class="nav-link px-0 py-1 d-flex align-items-center<?= $section === 'users' ? ' active fw-semibold text-dark' : ''; ?>">
                    <i class="bi bi-people me-2"></i>
                    <?= __('admin.sidebar.link.users'); ?>
                </a>
                <a href="/admin/orders"
                   class="nav-link px-0 py-1 d-flex align-items-center<?= $section === 'orders' ? ' active fw-semibold text-dark' : ''; ?>">
                    <i class="bi bi-receipt me-2"></i>
                    <?= __('admin.sidebar.link.orders'); ?>
                </a>

                <span class="text-uppercase text-muted fw-semibold mb-1 mt-3">
                    <?= __('admin.sidebar.group.catalog'); ?>
                </span>
                <a href="/admin/products"
                   class="nav-link px-0 py-1 d-flex align-items-center<?= $section === 'products' ? ' active fw-semibold text-dark' : ''; ?>">
                    <i class="bi bi-boxes me-2"></i>
                    <?= __('admin.sidebar.link.products'); ?>
                </a>
                <a href="/admin/categories"
                   class="nav-link px-0 py-1 d-flex align-items-center<?= $section === 'categories' ? ' active fw-semibold text-dark' : ''; ?>">
                    <i class="bi bi-grid-3x3-gap me-2"></i>
                    <?= __('admin.sidebar.link.categories'); ?>
                </a>
                <a href="/admin/brands"
                   class="nav-link px-0 py-1 d-flex align-items-center<?= $section === 'brands' ? ' active fw-semibold text-dark' : ''; ?>">
                    <i class="bi bi-badge-tm me-2"></i>
                    <?= __('admin.sidebar.link.brands'); ?>
                </a>

                <span class="text-uppercase text-muted fw-semibold mb-1 mt-3">
                    <?= __('admin.sidebar.group.marketing'); ?>
                </span>
                <a href="/admin/discounts"
                   class="nav-link px-0 py-1 d-flex align-items-center<?= $section === 'discounts' ? ' active fw-semibold text-dark' : ''; ?>">
                    <i class="bi bi-percent me-2"></i>
                    <?= __('admin.sidebar.link.discounts'); ?>
                </a>

                <a href="/admin/reviews/pending"
                   class="nav-link px-0 py-1 d-flex align-items-center<?= $section === 'reviews' ? ' active fw-semibold text-dark' : ''; ?>">
                    <i class="bi bi-chat-square-text me-2"></i>
                    <?= __('admin.sidebar.link.reviews'); ?>
                </a>

                <span class="text-uppercase text-muted fw-semibold mb-1 mt-3">
                    <?= __('admin.sidebar.group.service_content'); ?>
                </span>
                <a href="/admin/support"
                   class="nav-link px-0 py-1 d-flex align-items-center<?= $section === 'support' ? ' active fw-semibold text-dark' : ''; ?>">
                    <i class="bi bi-life-preserver me-2"></i>
                    <?= __('admin.sidebar.link.support'); ?>
                </a>
                <a href="/"
                   class="nav-link px-0 py-1 d-flex align-items-center mt-2">
                    <i class="bi bi-box-arrow-up-right me-2"></i>
                    <?= __('admin.sidebar.link.to_shop'); ?>
                </a>
            </nav>
        </div>
    </div>
</aside>
