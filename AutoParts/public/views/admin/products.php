<?php
/**
 * @var string|null $flash
 * @var array<int,array<string,mixed>> $products
 */

$flash    = $flash    ?? null;
$products = $products ?? [];

$searchQuery = (string)($_GET['q'] ?? '');
$confirmText = __('admin.products.index.confirm_delete', 'Видалити цей товар?');
?>
<section class="py-3 py-md-4">
    <div class="container-fluid">
        <div class="row">
            <?php include '_sidebar.php'; ?>

            <div class="col-12 col-lg-9 col-xl-10">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body p-3 p-md-4">

                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center me-3"
                                     style="width:56px;height:56px;">
                                    <i class="bi bi-boxes fs-3 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.products.index.title', 'Товари'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.products.index.subtitle', 'Керування товарами, цінами та активністю у каталозі.'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2">
                                <form class="input-group input-group-sm" method="get" action="/admin/products">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text"
                                           name="q"
                                           value="<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>"
                                           class="form-control border-start-0"
                                           placeholder="<?= __('admin.products.index.search_placeholder', 'Пошук за назвою / SKU / брендом'); ?>"
                                           aria-label="<?= __('admin.products.index.search_aria', 'Пошук товарів'); ?>">
                                </form>
                                <a href="/admin/products/create" class="btn btn-primary btn-sm fw-semibold">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    <?= __('admin.products.index.add_button', 'Додати товар'); ?>
                                </a>
                            </div>
                        </div>

                        <?php if (!empty($flash)): ?>
                            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light small text-muted">
                                <tr>
                                    <th><?= __('admin.products.index.th_id', 'ID'); ?></th>
                                    <th><?= __('admin.products.index.th_name', 'Назва'); ?></th>
                                    <th><?= __('admin.products.index.th_sku', 'SKU'); ?></th>
                                    <th><?= __('admin.products.index.th_brand', 'Бренд'); ?></th>
                                    <th><?= __('admin.products.index.th_category', 'Категорія'); ?></th>
                                    <th class="text-end"><?= __('admin.products.index.th_price', 'Ціна'); ?></th>
                                    <th><?= __('admin.products.index.th_status', 'Статус'); ?></th>
                                    <th class="text-end"><?= __('admin.products.index.th_actions', 'Дії'); ?></th>
                                </tr>
                                </thead>
                                <tbody class="small">
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <?php
                                        $id       = (string)($product['id'] ?? '');
                                        $name     = (string)($product['name'] ?? '—');
                                        $sku      = (string)($product['sku'] ?? '—');
                                        $brand    = (string)($product['brand_name'] ?? '—');
                                        $category = (string)($product['category_name'] ?? '—');
                                        $price    = (string)($product['price'] ?? '—');
                                        $currency = (string)($product['currency'] ?? 'UAH');
                                        $active   = !empty($product['is_active']);
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <a href="/admin/products/<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>"
                                                   class="text-decoration-none"
                                                   title="<?= __('admin.products.index.link_view', 'Переглянути товар'); ?>">
                                                    <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
                                                </a>
                                            </td>
                                            <td><?= htmlspecialchars($sku, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="text-end">
                                                <?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>
                                                <?= htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td>
                                                <?php if ($active): ?>
                                                    <span class="badge bg-success-subtle text-success">
                                                        <?= __('admin.products.index.status_active', 'Активний'); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary">
                                                        <?= __('admin.products.index.status_hidden', 'Прихований'); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="/admin/products/<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>/edit"
                                                   class="btn btn-outline-secondary btn-sm"
                                                   title="<?= __('admin.products.index.link_edit', 'Редагувати товар'); ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="/admin/products/<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>/delete"
                                                      method="post" class="d-inline"
                                                      onsubmit="return confirm(<?= json_encode($confirmText, JSON_UNESCAPED_UNICODE); ?>);">
                                                    <?= \App\Core\Csrf::csrfInput(); ?>
                                                    <button type="submit"
                                                            class="btn btn-outline-danger btn-sm"
                                                            title="<?= __('admin.products.index.link_delete', 'Видалити товар'); ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-3">
                                            <?= __('admin.products.index.empty', 'Товари поки що відсутні.'); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div><!-- /col main -->
        </div>
    </div>
</section>
