<?php
// path: app/Views/admin/category_edit.php

/**
 * @var array<string,mixed> $category
 * @var array<int,array<string,mixed>> $categories
 * @var string|null $flash
 */
$category   = $category ?? [];
$allCats    = $categories ?? [];
$currentId  = (int)($category['id'] ?? 0);
$name       = (string)($category['name'] ?? '');
$slug       = (string)($category['slug'] ?? '');
$parentId   = $category['parent_id'] ?? null;
$isActive   = (bool)($category['is_active'] ?? true);

// прибираємо поточну категорію зі списку батьків
$parentOptions = array_values(array_filter($allCats, static function ($c) use ($currentId) {
    return (int)($c['id'] ?? 0) !== $currentId;
}));

$section = 'categories';
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
                                <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center me-3" style="width:56px;height:56px;">
                                    <i class="bi bi-grid-3x3-gap fs-3 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.categories.edit.title', 'Редагування категорії'); ?>
                                        #<?= htmlspecialchars((string)$currentId, ENT_QUOTES, 'UTF-8'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.categories.edit.subtitle', 'Оновіть дані категорії та збережіть зміни.'); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="/admin/categories" class="btn btn-outline-secondary btn-sm fw-semibold">
                                    <i class="bi bi-arrow-left"></i>
                                    <?= __('admin.categories.edit.back', 'Назад'); ?>
                                </a>
                            </div>
                        </div>

                        <?php if (!empty($flash)): ?>
                            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= __('common.close', 'Закрити'); ?>"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post"
                            action="/admin/categories/<?= htmlspecialchars((string)$currentId, ENT_QUOTES, 'UTF-8'); ?>/update"
                            class="needs-validation"
                            novalidate>
                            <?= \App\Core\Csrf::csrfInput(); ?>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small text-muted">
                                        <?= __('admin.categories.edit.name', 'Назва'); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        name="name"
                                        class="form-control"
                                        value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                        id="cat-name">
                                    <div class="invalid-feedback">
                                        <?= __('admin.categories.edit.name_required', 'Вкажіть назву категорії.'); ?>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label small text-muted">
                                        <?= __('admin.categories.edit.slug', 'Slug'); ?>
                                    </label>
                                    <input type="text"
                                        name="slug"
                                        class="form-control"
                                        value="<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>"
                                        id="cat-slug"
                                        placeholder="<?= __('admin.categories.edit.slug_placeholder', 'Якщо залишити порожнім — згенерується автоматично'); ?>">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label small text-muted">
                                        <?= __('admin.categories.edit.parent', 'Батьківська категорія'); ?>
                                    </label>
                                    <select name="parent_id" class="form-select">
                                        <option value="">
                                            <?= __('admin.categories.edit.parent_none', '— Без батьківської —'); ?>
                                        </option>
                                        <?php foreach ($parentOptions as $opt): ?>
                                            <?php
                                            $optId   = (int)($opt['id'] ?? 0);
                                            $optName = (string)($opt['name'] ?? '');
                                            $selected = ($parentId !== null && (int)$parentId === $optId) ? 'selected' : '';
                                            ?>
                                            <option value="<?= htmlspecialchars((string)$optId, ENT_QUOTES, 'UTF-8'); ?>" <?= $selected; ?>>
                                                <?= htmlspecialchars($optName, ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                            <?= $isActive ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">
                                            <?= __('admin.categories.edit.active', 'Активна'); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary fw-semibold">
                                    <i class="bi bi-save"></i>
                                    <?= __('admin.categories.edit.save_button', 'Зберегти'); ?>
                                </button>
                                <a href="/admin/categories" class="btn btn-outline-secondary">
                                    <?= __('admin.categories.edit.cancel_button', 'Скасувати'); ?>
                                </a>

                                <form method="post"
                                    action="/admin/categories/<?= htmlspecialchars((string)$currentId, ENT_QUOTES, 'UTF-8'); ?>/delete"
                                    class="ms-auto"
                                    onsubmit="return confirm('<?= addslashes(__('admin.categories.edit.delete_confirm', 'Видалити категорію?')); ?>');">
                                    <?= \App\Core\Csrf::csrfInput(); ?>
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </form>

                    </div>
                </div>
            </div><!-- /col main -->
        </div>
    </div>
</section>

<script>
    /* Чому JS: юзер-удобність — автогенерація slug, якщо він порожній */
    (function() {
        const nameInput = document.getElementById('cat-name');
        const slugInput = document.getElementById('cat-slug');
        if (!nameInput || !slugInput) return;

        const slugify = (s) => s
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9\-]/g, '')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');

        nameInput.addEventListener('input', function() {
            if (slugInput.value.trim() === '') {
                slugInput.value = slugify(this.value);
            }
        });
    })();
</script>