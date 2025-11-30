<?php
/**
 * @var array<string,mixed>|null $brand
 * @var string|null $flash
 */
$brand   = $brand ?? null;

$isEdit  = $brand && !empty($brand['id']);
$id      = $isEdit ? (int)$brand['id'] : 0;

$name     = (string)($brand['name']      ?? '');
$slug     = (string)($brand['slug']      ?? '');
$logoUrl  = (string)($brand['logo_url']  ?? '');
$isActive = !isset($brand['is_active']) || (bool)$brand['is_active'];

$section  = 'brands';

$formAction = $isEdit
    ? "/admin/brands/{$id}/update"
    : "/admin/brands/store";

// заголовок з підстановкою :id
$title = $isEdit
    ? str_replace(':id', (string)$id, __('admin.brands.edit.title_edit', 'Редагування бренду #:id'))
    : __('admin.brands.edit.title_create', 'Створення бренду');
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
                                    <i class="bi bi-badge-tm fs-3 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.brands.edit.subtitle', 'Назва, slug, логотип та активність бренду.'); ?>
                                    </p>
                                </div>
                            </div>

                            <a href="/admin/brands" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i>
                                <?= __('admin.brands.edit.back', 'До списку брендів'); ?>
                            </a>
                        </div>

                        <?php if (!empty($flash)): ?>
                            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="<?= __('common.close', 'Закрити'); ?>"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post"
                              action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8'); ?>"
                              class="row g-3">
                            <?= \App\Core\Csrf::csrfInput(); ?>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">
                                    <?= __('admin.brands.edit.name', 'Назва бренду'); ?>
                                </label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       required
                                       value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">
                                    <?= __('admin.brands.edit.slug', 'Slug (URL)'); ?>
                                </label>
                                <input type="text"
                                       name="slug"
                                       class="form-control"
                                       placeholder="<?= __('admin.brands.edit.slug_placeholder', 'наприклад, bmw, audi'); ?>"
                                       value="<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="form-text small">
                                    <?= __('admin.brands.edit.slug_help', 'Якщо залишити порожнім, slug можна згенерувати автоматично на API.'); ?>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">
                                    <?= __('admin.brands.edit.logo_url', 'Посилання на логотип'); ?>
                                </label>
                                <input type="text"
                                       name="logo_url"
                                       class="form-control"
                                       placeholder="https://..."
                                       value="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="form-text small">
                                    <?= __('admin.brands.edit.logo_help', 'Можна вказати абсолютний або відносний шлях до зображення.'); ?>
                                </div>
                            </div>

                            <?php if ($logoUrl !== ''): ?>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">
                                        <?= __('admin.brands.edit.logo_current', 'Поточний логотип'); ?>
                                    </label><br>
                                    <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                         alt=""
                                         class="img-thumbnail"
                                         style="max-height:48px;">
                                </div>
                            <?php endif; ?>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="brandActive"
                                           name="is_active"
                                           <?= $isActive ? 'checked' : ''; ?>>
                                    <label class="form-check-label small" for="brandActive">
                                        <?= __('admin.brands.edit.active_label', 'Бренд активний (видимий на сайті)'); ?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-between mt-3">
                                <a href="/admin/brands" class="btn btn-outline-secondary">
                                    <?= __('admin.brands.edit.cancel_button', 'Скасувати'); ?>
                                </a>
                                <button type="submit" class="btn btn-primary fw-semibold">
                                    <?= __('admin.brands.edit.save_button', 'Зберегти зміни'); ?>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div><!-- /col main -->
        </div>
    </div>
</section>
