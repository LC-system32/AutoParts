<?php
/**
 * @var string|null $flash
 * @var array<int,array<string,mixed>> $brands
 */

$flash  = $flash  ?? null;
$brands = $brands ?? [];
$section = 'brands';
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
                                        <?= __('admin.brands.index.title'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.brands.index.subtitle'); ?>
                                    </p>
                                </div>
                            </div>

                            <form class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2"
                                  method="post" action="/admin/brands/store">
                                <?= \App\Core\Csrf::csrfInput(); ?>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-plus-circle"></i>
                                    </span>
                                    <input type="text"
                                           name="name"
                                           class="form-control border-start-0"
                                           placeholder="<?= __('admin.brands.index.form.new.placeholder'); ?>"
                                           required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                                    <?= __('admin.brands.index.form.new.submit'); ?>
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
                                    <th><?= __('admin.brands.index.table.header.id'); ?></th>
                                    <th><?= __('admin.brands.index.table.header.name'); ?></th>
                                    <th><?= __('admin.brands.index.table.header.slug'); ?></th>
                                    <th><?= __('admin.brands.index.table.header.logo'); ?></th>
                                    <th class="text-end"><?= __('admin.brands.index.table.header.actions'); ?></th>
                                </tr>
                                </thead>
                                <tbody class="small">
                                <?php if (!empty($brands)): ?>
                                    <?php foreach ($brands as $brand): ?>
                                        <?php
                                        $bid   = (string)($brand['id'] ?? '');
                                        $bname = (string)($brand['name'] ?? '—');
                                        $slug  = (string)($brand['slug'] ?? '—');
                                        $logo  = (string)($brand['logo_url'] ?? '');
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($bid, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars($bname, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <?php if ($logo !== ''): ?>
                                                    <img src="<?= htmlspecialchars($logo, ENT_QUOTES, 'UTF-8'); ?>"
                                                         alt="<?= htmlspecialchars($bname, ENT_QUOTES, 'UTF-8'); ?>"
                                                         class="img-thumbnail"
                                                         style="max-height:32px;">
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="/admin/brands/<?= htmlspecialchars($bid, ENT_QUOTES, 'UTF-8'); ?>/edit"
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="/admin/brands/<?= htmlspecialchars($bid, ENT_QUOTES, 'UTF-8'); ?>/delete"
                                                      method="post"
                                                      class="d-inline"
                                                      onsubmit="return confirm(<?= json_encode(__('admin.brands.index.delete.confirm')); ?>);">
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
                                            <?= __('admin.brands.index.empty'); ?>
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
