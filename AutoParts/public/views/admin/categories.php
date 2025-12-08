<?php
/**
 * @var string|null $flash
 * @var array<int,array<string,mixed>> $categories
 */

$flash      = $flash      ?? null;
$categories = $categories ?? [];
$section    = 'categories';

// Чому: API списку не повертає parent_name. Готуємо швидкий пошук.
$nameById = [];
foreach ($categories as $c) {
    $cid = (int)($c['id'] ?? 0);
    if ($cid > 0) {
        $nameById[$cid] = (string)($c['name'] ?? '');
    }
}
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
                                    <i class="bi bi-grid-3x3-gap fs-3 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.categories.index.title'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.categories.index.subtitle'); ?>
                                    </p>
                                </div>
                            </div>

                            <form class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2"
                                  method="post" action="/admin/categories/store">
                                <?= \App\Core\Csrf::csrfInput(); ?>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-plus-circle"></i>
                                    </span>
                                    <input type="text"
                                           name="name"
                                           class="form-control border-start-0"
                                           placeholder="<?= __('admin.categories.index.form.new.placeholder'); ?>"
                                           required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                                    <?= __('admin.categories.index.form.new.submit'); ?>
                                </button>
                            </form>
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

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light small text-muted">
                                <tr>
                                    <th><?= __('admin.categories.index.table.header.id'); ?></th>
                                    <th><?= __('admin.categories.index.table.header.name'); ?></th>
                                    <th><?= __('admin.categories.index.table.header.slug'); ?></th>
                                    <th><?= __('admin.categories.index.table.header.parent'); ?></th>
                                    <th class="text-end"><?= __('admin.categories.index.table.header.actions'); ?></th>
                                </tr>
                                </thead>
                                <tbody class="small">
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <?php
                                            $id   = (int)($cat['id'] ?? 0);
                                            $pid  = $cat['parent_id'] ?? null;

                                            // fallback: беремо parent_name якщо вже є, або шукаємо за parent_id
                                            $parentName = (string)($cat['parent_name'] ?? '');
                                            if ($parentName === '' && $pid !== null && $pid !== '' && isset($nameById[(int)$pid])) {
                                                $parentName = $nameById[(int)$pid];
                                            }
                                            $hasParent = ($pid !== null && $pid !== '' && isset($nameById[(int)$pid]));
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars((string)($cat['name'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars((string)($cat['slug'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <?php if ($hasParent): ?>
                                                    <a href="/admin/categories/<?= htmlspecialchars((string)(int)$pid, ENT_QUOTES, 'UTF-8'); ?>/edit"
                                                       class="text-decoration-none">
                                                        <?= htmlspecialchars($parentName, ENT_QUOTES, 'UTF-8'); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="/admin/categories/<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>/edit"
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="/admin/categories/<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>/delete"
                                                      method="post" class="d-inline"
                                                      onsubmit="return confirm(<?= json_encode(__('admin.categories.index.delete.confirm')); ?>);">
                                                    <?= \App\Core\Csrf::csrfInput(); ?>
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <?= __('admin.categories.index.empty'); ?>
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
