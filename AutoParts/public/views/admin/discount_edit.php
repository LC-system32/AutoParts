<?php
/**
 * @var array<string,mixed>|null $discount
 * @var string|null $flash
 */
$discount = $discount ?? null;
$id       = (int)($discount['id'] ?? 0);

// YYYY-MM-DD для <input type="date"> з локальних полів
$dateFrom = (string)($discount['date_from_local'] ?? '');
if ($dateFrom === '' && !empty($discount['date_from'])) {
    $dateFrom = substr((string)$discount['date_from'], 0, 10);
}

$dateTo = (string)($discount['date_to_local'] ?? '');
if ($dateTo === '' && !empty($discount['date_to'])) {
    $dateTo = substr((string)$discount['date_to'], 0, 10);
}
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
                                    <?= __('admin.discounts.edit.title', 'Редагування знижки'); ?>
                                </h1>
                                <p class="text-muted small mb-0">
                                    <?= __('admin.discounts.edit.subtitle', 'Налаштування параметрів акції / купона.'); ?>
                                </p>
                            </div>
                        </div>

                        <?php if (!empty($flash)): ?>
                            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="<?= htmlspecialchars(__('common.close', 'Закрити'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="/admin/discounts/<?= $id; ?>/update">
                            <?= \App\Core\Csrf::csrfInput(); ?>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <?= __('admin.discounts.edit.name_label', 'Назва акції'); ?>
                                    </label>
                                    <input type="text"
                                           name="name"
                                           class="form-control"
                                           required
                                           value="<?= htmlspecialchars((string)($discount['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <?= __('admin.discounts.edit.code_label', 'Код купона'); ?>
                                    </label>
                                    <input type="text"
                                           name="code"
                                           class="form-control"
                                           value="<?= htmlspecialchars((string)($discount['code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        <?= __('admin.discounts.edit.description_label', 'Опис'); ?>
                                    </label>
                                    <textarea name="description"
                                              class="form-control"
                                              rows="3"><?= htmlspecialchars((string)($discount['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <?= __('admin.discounts.edit.type_label', 'Тип знижки'); ?>
                                    </label>
                                    <?php $type = (string)($discount['discount_type'] ?? 'percent'); ?>
                                    <select name="discount_type" class="form-select" required>
                                        <option value="percent" <?= $type === 'percent' ? 'selected' : ''; ?>>
                                            <?= __('admin.discounts.edit.type_percent', 'Відсоток (%)'); ?>
                                        </option>
                                        <option value="fixed" <?= $type === 'fixed' ? 'selected' : ''; ?>>
                                            <?= __('admin.discounts.edit.type_fixed', 'Фіксована сума (грн)'); ?>
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <?= __('admin.discounts.edit.value_label', 'Розмір знижки'); ?>
                                    </label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           name="value"
                                           class="form-control"
                                           required
                                           value="<?= htmlspecialchars((string)($discount['value'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <?= __('admin.discounts.edit.min_sum_label', 'Мін. сума замовлення (грн)'); ?>
                                    </label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           name="min_order_sum"
                                           class="form-control"
                                           value="<?= htmlspecialchars((string)($discount['min_order_sum'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <?= __('admin.discounts.edit.date_from_label', 'Початок дії'); ?>
                                    </label>
                                    <input type="date"
                                           name="date_from"
                                           class="form-control"
                                           value="<?= htmlspecialchars($dateFrom, ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <?= __('admin.discounts.edit.date_to_label', 'Кінець дії'); ?>
                                    </label>
                                    <input type="date"
                                           name="date_to"
                                           class="form-control"
                                           value="<?= htmlspecialchars($dateTo, ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="discount_is_active"
                                               name="is_active"
                                               <?= (!empty($discount['is_active']) || !empty($discount['active'])) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="discount_is_active">
                                            <?= __('admin.discounts.edit.active_label', 'Активна знижка'); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check2"></i>
                                    <?= __('admin.discounts.edit.button_save', 'Зберегти'); ?>
                                </button>
                                <a href="/admin/discounts" class="btn btn-outline-secondary">
                                    <?= __('admin.discounts.edit.button_back', 'Назад до списку'); ?>
                                </a>
                            </div>
                        </form>

                    </div>
                </div>
            </div><!-- /col main -->
        </div>
    </div>
</section>
