<?php

/**
 * @var string|null                       $flash
 * @var array<int,array<string,mixed>>    $discounts
 */

$flash     = $flash     ?? null;
$discounts = $discounts ?? [];

$section = 'discounts';
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
                                    <i class="bi bi-percent fs-3 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.discounts.index.title'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.discounts.index.subtitle'); ?>
                                    </p>
                                </div>
                            </div>

                            <a href="/admin/discounts/create" class="btn btn-success btn-sm fw-semibold">
                                <i class="bi bi-plus-circle me-1"></i>
                                <?= __('admin.discounts.index.create'); ?>
                            </a>
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
                                    <th><?= __('admin.discounts.index.table.header.name'); ?></th>
                                    <th><?= __('admin.discounts.index.table.header.code'); ?></th>
                                    <th class="text-end"><?= __('admin.discounts.index.table.header.value'); ?></th>
                                    <th><?= __('admin.discounts.index.table.header.period'); ?></th>
                                    <th><?= __('admin.discounts.index.table.header.status'); ?></th>
                                    <th class="text-end"><?= __('admin.discounts.index.table.header.actions'); ?></th>
                                </tr>
                                </thead>
                                <tbody class="small">
                                <?php if (!empty($discounts)): ?>
                                    <?php foreach ($discounts as $d): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars((string)($d['name'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td>
                                                <code>
                                                    <?= htmlspecialchars((string)($d['code'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                </code>
                                            </td>
                                            <td class="text-end">
                                                <?= htmlspecialchars(
                                                    (string)($d['value_label'] ?? $d['value'] ?? '—'),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars((string)($d['date_from'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                —
                                                <?= htmlspecialchars((string)($d['date_to'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td>
                                                <?php $active = !empty($d['is_active']); ?>
                                                <span class="badge <?= $active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>">
                                                    <?= $active
                                                        ? __('admin.discounts.index.status.active')
                                                        : __('admin.discounts.index.status.inactive'); ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="/admin/discounts/<?= htmlspecialchars((string)($d['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>/edit"
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="/admin/discounts/<?= htmlspecialchars((string)($d['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>/delete"
                                                      method="post"
                                                      class="d-inline"
                                                      onsubmit="return confirm(<?= json_encode(__('admin.discounts.index.delete.confirm')); ?>);">
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
                                        <td colspan="6" class="text-center text-muted py-3">
                                            <?= __('admin.discounts.index.empty'); ?>
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
