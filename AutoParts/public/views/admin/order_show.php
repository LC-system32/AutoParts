<?php

/**
 * @var string|null                 $flash
 * @var array<string,mixed>         $order
 * @var array<int,array<string,mixed>> $items
 */

$flash = $flash ?? null;
$order = $order ?? [];
$items = $items ?? [];

$section = 'orders';

$orderId     = (int)($order['id'] ?? 0);
$statusCode  = (string)($order['status_code'] ?? '');
$totalAmount = (string)($order['total_amount'] ?? '—');
$currency    = (string)($order['currency'] ?? 'UAH');
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
                                     style="width:48px;height:48px;">
                                    <i class="bi bi-receipt-cutoff fs-4 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.orders.show.title'); ?>
                                        <?= htmlspecialchars((string)($order['order_number'] ?? $orderId), ENT_QUOTES, 'UTF-8'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.orders.show.subtitle'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row gap-2">
                                <a href="/admin/orders" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left-short me-1"></i>
                                    <?= __('admin.orders.show.back'); ?>
                                </a>
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

                        <div class="row g-3">
                            <!-- Інформація про замовлення -->
                            <div class="col-12 col-xl-7">
                                <div class="border rounded-4 p-3 bg-white h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-muted text-uppercase fw-semibold">
                                            <?= __('admin.orders.show.section.main'); ?>
                                        </span>
                                        <span class="badge bg-light text-muted text-uppercase">
                                            <?= $statusCode !== ''
                                                ? __('order.status.' . $statusCode)
                                                : '—'; ?>
                                        </span>
                                    </div>

                                    <dl class="row small mb-0">
                                        <dt class="col-4 text-muted">
                                            <?= __('admin.orders.show.created_at'); ?>
                                        </dt>
                                        <dd class="col-8">
                                            <?= htmlspecialchars((string)($order['created_at'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                        </dd>

                                        <dt class="col-4 text-muted">
                                            <?= __('admin.orders.show.total'); ?>
                                        </dt>
                                        <dd class="col-8">
                                            <span class="fw-semibold">
                                                <?= htmlspecialchars($totalAmount, ENT_QUOTES, 'UTF-8'); ?>
                                                <?= htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </dd>

                                        <dt class="col-4 text-muted">
                                            <?= __('admin.orders.show.payment'); ?>
                                        </dt>
                                        <dd class="col-8">
                                            <?php $paid = $order['is_paid'] ?? false; ?>
                                            <?php if ($paid): ?>
                                                <span class="badge bg-success-subtle text-success">
                                                    <?= __('admin.orders.show.payment.paid'); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    <?= __('admin.orders.show.payment.not_paid'); ?>
                                                </span>
                                            <?php endif; ?>
                                        </dd>

                                        <dt class="col-4 text-muted">
                                            <?= __('admin.orders.show.payment_method'); ?>
                                        </dt>
                                        <dd class="col-8">
                                            <?= htmlspecialchars((string)($order['payment_method'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                        </dd>

                                        <dt class="col-4 text-muted">
                                            <?= __('admin.orders.show.delivery_method'); ?>
                                        </dt>
                                        <dd class="col-8">
                                            <?= htmlspecialchars((string)($order['delivery_method'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                        </dd>
                                    </dl>
                                </div>
                            </div>

                            <!-- Клієнт -->
                            <div class="col-12 col-xl-5">
                                <div class="border rounded-4 p-3 bg-white h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-muted text-uppercase fw-semibold">
                                            <?= __('admin.orders.show.section.customer'); ?>
                                        </span>
                                        <?php if (!empty($order['user_id'])): ?>
                                            <a href="/admin/users/<?= (int)$order['user_id']; ?>/edit"
                                               class="small text-decoration-none">
                                                <?= __('admin.orders.show.customer.open_profile'); ?>
                                                <i class="bi bi-arrow-right-short"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <dl class="row small mb-0">
                                        <dt class="col-4 text-muted">
                                            <?= __('admin.orders.show.customer.name'); ?>
                                        </dt>
                                        <dd class="col-8">
                                            <?= htmlspecialchars((string)($order['customer_name'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                        </dd>

                                        <dt class="col-4 text-muted">
                                            <?= __('admin.orders.show.customer.phone'); ?>
                                        </dt>
                                        <dd class="col-8">
                                            <?= htmlspecialchars((string)($order['customer_phone'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                        </dd>

                                        <dt class="col-4 text-muted">
                                            <?= __('admin.orders.show.customer.email'); ?>
                                        </dt>
                                        <dd class="col-8">
                                            <?= htmlspecialchars((string)($order['customer_email'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                        </dd>

                                        <dt class="col-4 text-muted">
                                            <?= __('admin.orders.show.customer.address'); ?>
                                        </dt>
                                        <dd class="col-8">
                                            <?= nl2br(htmlspecialchars((string)($order['shipping_address'] ?? '—'), ENT_QUOTES, 'UTF-8')); ?>
                                        </dd>

                                        <?php if (!empty($order['comment'])): ?>
                                            <dt class="col-4 text-muted">
                                                <?= __('admin.orders.show.customer.comment'); ?>
                                            </dt>
                                            <dd class="col-8">
                                                <?= nl2br(htmlspecialchars((string)$order['comment'], ENT_QUOTES, 'UTF-8')); ?>
                                            </dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            </div>

                            <!-- Товари замовлення -->
                            <div class="col-12">
                                <div class="border rounded-4 p-3 bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-muted text-uppercase fw-semibold">
                                            <?= __('admin.orders.show.section.items'); ?>
                                        </span>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light small text-muted">
                                            <tr>
                                                <th><?= __('admin.orders.show.items.header.product'); ?></th>
                                                <th class="text-center"><?= __('admin.orders.show.items.header.qty'); ?></th>
                                                <th class="text-end"><?= __('admin.orders.show.items.header.price'); ?></th>
                                                <th class="text-end"><?= __('admin.orders.show.items.header.total'); ?></th>
                                            </tr>
                                            </thead>
                                            <tbody class="small">
                                            <?php if (!empty($items)): ?>
                                                <?php foreach ($items as $item): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="fw-semibold">
                                                                <?= htmlspecialchars((string)($item['product_name'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                            </div>
                                                            <div class="text-muted">
                                                                <?= __('admin.orders.show.items.sku_prefix'); ?>
                                                                <?= htmlspecialchars((string)($item['sku'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <?= (int)($item['quantity'] ?? 0); ?>
                                                        </td>
                                                        <td class="text-end">
                                                            <?= htmlspecialchars((string)($item['price'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                        </td>
                                                        <td class="text-end">
                                                            <?= htmlspecialchars((string)($item['total'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">
                                                        <?= __('admin.orders.show.items.empty'); ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Форма зміни статусу -->
                            <div class="col-12 col-lg-6">
                                <div class="border rounded-4 p-3 bg-white">
                                    <div class="small text-muted text-uppercase fw-semibold mb-2">
                                        <?= __('admin.orders.show.section.status_update'); ?>
                                    </div>
                                    <form method="post" action="/admin/orders/<?= (int)$order['id']; ?>/status">
                                        <?= \App\Core\Csrf::csrfInput(); ?>

                                        <div class="col-12 col-md-6">
                                            <label class="form-label small text-muted mb-1">
                                                <?= __('admin.orders.show.status.label'); ?>
                                            </label>
                                            <select name="status" class="form-select form-select-sm">
                                                <?php
                                                $status = (string)($order['status_code'] ?? '');
                                                $options = [
                                                    'new',
                                                    'pending',
                                                    'paid',
                                                    'shipped',
                                                    'completed',
                                                    'cancelled',
                                                ];
                                                ?>
                                                <?php foreach ($options as $value): ?>
                                                    <option value="<?= $value; ?>" <?= $status === $value ? 'selected' : ''; ?>>
                                                        <?= __('order.status.' . $value); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6 mt-3">
                                            <label class="form-label small text-muted mb-1">
                                                <?= __('admin.orders.show.payment.label'); ?>
                                            </label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       id="is_paid"
                                                       name="is_paid"
                                                    <?= !empty($order['is_paid']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label small" for="is_paid">
                                                    <?= __('admin.orders.show.payment.mark_paid'); ?>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-3">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="bi bi-check2-circle me-1"></i>
                                                <?= __('admin.orders.show.status.save'); ?>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div><!-- /row -->
                    </div>
                </div>
            </div><!-- /main -->
        </div>
    </div>
</section>
