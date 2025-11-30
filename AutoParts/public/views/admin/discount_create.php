<?php
/** @var string|null $flash */

$flash   = $flash ?? null;
$section = 'discounts';
?>
<section class="py-3 py-md-4">
    <div class="container-fluid">
        <div class="row">
            <?php include '_sidebar.php'; ?>

            <div class="col-12 col-lg-9 col-xl-10">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body p-3 p-md-4">

                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center me-3"
                                 style="width:56px;height:56px;">
                                <i class="bi bi-percent fs-3 text-dark"></i>
                            </div>
                            <div>
                                <h1 class="h4 fw-bold mb-1">
                                    <?= __('admin.discounts.create.title'); ?>
                                </h1>
                                <p class="text-muted small mb-0">
                                    <?= __('admin.discounts.create.subtitle'); ?>
                                </p>
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

                        <form method="post" action="/admin/discounts/store">
                            <?= \App\Core\Csrf::csrfInput(); ?>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <?= __('admin.discounts.create.form.name'); ?>
                                    </label>
                                    <input type="text"
                                           name="name"
                                           class="form-control"
                                           required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <?= __('admin.discounts.create.form.code'); ?>
                                    </label>
                                    <input type="text"
                                           name="code"
                                           class="form-control"
                                           placeholder="<?= __('admin.discounts.create.form.code.placeholder'); ?>">
                                    <div class="form-text">
                                        <?= __('admin.discounts.create.form.code.help'); ?>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        <?= __('admin.discounts.create.form.description'); ?>
                                    </label>
                                    <textarea name="description"
                                              class="form-control"
                                              rows="3"
                                              placeholder="<?= __('admin.discounts.create.form.description.placeholder'); ?>"></textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <?= __('admin.discounts.create.form.type'); ?>
                                    </label>
                                    <select name="discount_type" class="form-select" required>
                                        <option value="percent">
                                            <?= __('admin.discounts.create.form.type.percent'); ?>
                                        </option>
                                        <option value="fixed">
                                            <?= __('admin.discounts.create.form.type.fixed'); ?>
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <?= __('admin.discounts.create.form.value'); ?>
                                    </label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           name="value"
                                           class="form-control"
                                           required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <?= __('admin.discounts.create.form.min_order_sum'); ?>
                                    </label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           name="min_order_sum"
                                           class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <?= __('admin.discounts.create.form.date_from'); ?>
                                    </label>
                                    <input type="date"
                                           name="date_from"
                                           class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <?= __('admin.discounts.create.form.date_to'); ?>
                                    </label>
                                    <input type="date"
                                           name="date_to"
                                           class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="discount_active"
                                               name="active"
                                               checked>
                                        <label class="form-check-label" for="discount_active">
                                            <?= __('admin.discounts.create.form.active'); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check2"></i>
                                    <?= __('admin.discounts.create.actions.submit'); ?>
                                </button>
                                <a href="/admin/discounts" class="btn btn-outline-secondary">
                                    <?= __('admin.discounts.create.actions.back'); ?>
                                </a>
                            </div>
                        </form>

                    </div>
                </div>
            </div><!-- /col main -->
        </div>
    </div>
</section>
