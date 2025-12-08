<?php
/**
 * @var string|null                     $flash
 * @var array<string,mixed>|null        $product
 * @var array<int,array<string,mixed>>  $brands
 * @var array<int,array<string,mixed>>  $categories
 */

$flash      = $flash      ?? null;
$product    = $product    ?? null;
$brands     = $brands     ?? [];
$categories = $categories ?? [];

$section = 'products';

$isEdit = $product && !empty($product['id']);
$id     = $isEdit ? (int)$product['id'] : null;

$name        = $product['name']              ?? '';
$sku         = $product['sku']               ?? '';
$brandId     = $product['brand_id']          ?? '';
$categoryId  = $product['category_id']       ?? '';
$shortDesc   = $product['short_description'] ?? '';
$fullDesc    = $product['description']       ?? ($product['full_description'] ?? '');
$isActive    = !empty($product['is_active']);

// Шлях форми – підлаштуй під свої роуты, якщо інші
$formAction = $isEdit
    ? "/admin/products/{$id}/update"
    : "/admin/products/store";
?>

<section class="py-3 py-md-4">
    <div class="container-fluid">
        <div class="row">
            <?php include '_sidebar.php'; ?>

            <div class="col-12 col-lg-9 col-xl-10">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body p-3 p-md-4">

                        <!-- Заголовок -->
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center me-3"
                                     style="width:56px;height:56px;">
                                    <i class="bi bi-boxes fs-3 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= $isEdit
                                            ? __('admin.products.form.title.edit')
                                            : __('admin.products.form.title.create'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= $isEdit
                                            ? __('admin.products.form.subtitle.edit')
                                            : __('admin.products.form.subtitle.create'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2">
                                <a href="/admin/products"
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    <?= __('admin.products.form.back'); ?>
                                </a>
                                <?php if ($isEdit): ?>
                                    <a href="/admin/products/<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>"
                                       class="btn btn-light btn-sm">
                                        <i class="bi bi-eye me-1"></i>
                                        <?= __('admin.products.form.view'); ?>
                                    </a>
                                <?php endif; ?>
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

                        <form method="post" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php
                            // CSRF
                            if (method_exists(\App\Core\Csrf::class, 'csrfInput')) {
                                echo \App\Core\Csrf::csrfInput();
                            } else {
                                ?>
                                <input type="hidden" name="_csrf"
                                       value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php
                            }
                            ?>

                            <div class="row g-3 g-md-4">
                                <div class="col-12 col-lg-8">

                                    <div class="mb-3">
                                        <label class="form-label small text-muted mb-1">
                                            <?= __('admin.products.form.name'); ?>
                                        </label>
                                        <input type="text"
                                               name="name"
                                               class="form-control"
                                               required
                                               value="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label small text-muted mb-1">
                                                <?= __('admin.products.form.sku'); ?>
                                            </label>
                                            <input type="text"
                                                   name="sku"
                                                   class="form-control form-control-sm"
                                                   value="<?= htmlspecialchars((string)$sku, ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small text-muted mb-1">
                                                <?= __('admin.products.form.brand'); ?>
                                            </label>
                                            <select name="brand_id" class="form-select form-select-sm">
                                                <option value="">
                                                    <?= __('admin.products.form.not_set'); ?>
                                                </option>
                                                <?php foreach ($brands as $brand): ?>
                                                    <?php $bid = (int)($brand['id'] ?? 0); ?>
                                                    <option value="<?= $bid; ?>"
                                                        <?= (string)$brandId === (string)$bid ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars((string)($brand['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small text-muted mb-1">
                                                <?= __('admin.products.form.category'); ?>
                                            </label>
                                            <select name="category_id" class="form-select form-select-sm">
                                                <option value="">
                                                    <?= __('admin.products.form.not_set'); ?>
                                                </option>
                                                <?php foreach ($categories as $cat): ?>
                                                    <?php $cid = (int)($cat['id'] ?? 0); ?>
                                                    <option value="<?= $cid; ?>"
                                                        <?= (string)$categoryId === (string)$cid ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars((string)($cat['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label class="form-label small text-muted mb-1">
                                            <?= __('admin.products.form.short_description'); ?>
                                        </label>
                                        <textarea name="short_description"
                                                  class="form-control"
                                                  rows="2"><?= htmlspecialchars((string)$shortDesc, ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>

                                    <div class="mt-3">
                                        <label class="form-label small text-muted mb-1">
                                            <?= __('admin.products.form.full_description'); ?>
                                        </label>
                                        <textarea name="description"
                                                  class="form-control"
                                                  rows="6"><?= htmlspecialchars((string)$fullDesc, ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>

                                </div>

                                <div class="col-12 col-lg-4">
                                    <div class="border rounded-4 p-3 h-100 bg-light">
                                        <h2 class="h6 fw-semibold mb-3">
                                            <?= __('admin.products.form.publish.title'); ?>
                                        </h2>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   role="switch"
                                                   id="is_active"
                                                   name="is_active"
                                                <?= $isActive ? 'checked' : ''; ?>>
                                            <label class="form-check-label small" for="is_active">
                                                <?= __('admin.products.form.publish.active_label'); ?>
                                            </label>
                                        </div>

                                        <?php if ($isEdit): ?>
                                            <div class="small text-muted mb-3">
                                                <div>
                                                    <i class="bi bi-clock-history me-1"></i>
                                                    <?= __('admin.products.form.id'); ?>:
                                                    <?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-dark btn-sm fw-semibold">
                                                <i class="bi bi-save me-1"></i>
                                                <?= $isEdit
                                                    ? __('admin.products.form.save')
                                                    : __('admin.products.form.create'); ?>
                                            </button>
                                            <a href="/admin/products"
                                               class="btn btn-outline-secondary btn-sm">
                                                <?= __('admin.products.form.cancel'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /row -->

                        </form>

                    </div>
                </div>
            </div><!-- /col main -->
        </div>
    </div>
</section>
