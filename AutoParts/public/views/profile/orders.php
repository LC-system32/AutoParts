<?php
/** @var array<int, array<string, mixed>> $orders */
/** @var string|null $flash */

$orders = $orders ?? [];
$flash  = $flash  ?? null;

// Проста агрегована статистика по замовленнях
$totalOrders   = count($orders);
$totalAmount   = 0.0;
$paidCount     = 0;

foreach ($orders as $o) {
    $amount = (float)($o['total_amount'] ?? $o['total'] ?? 0);
    $totalAmount += $amount;

    $statusCode = mb_strtolower((string)($o['status_code'] ?? $o['status'] ?? ''));
    if (in_array($statusCode, ['paid', 'completed', 'оплачено', 'завершено'], true)) {
        $paidCount++;
    }
}
?>

<section class="py-4">

    <!-- HERO-БЛОК -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div class="mb-3 mb-md-0">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center me-2"
                     style="width:44px;height:44px;">
                    <i class="bi bi-receipt-cutoff fs-5 text-warning"></i>
                </div>
                <div>
                    <h1 class="h4 fw-bold mb-1">
                        <?= __('page.orders.title'); ?>
                    </h1>
                    <p class="text-muted small mb-0">
                        <?= __('page.orders.hero.subtitle'); ?>
                    </p>
                </div>
            </div>
            <div class="small text-muted mt-1">
                <?= __('page.orders.breadcrumb'); ?>
            </div>
        </div>

        <?php if ($totalOrders > 0): ?>
            <?php
            $lastCreatedAt = $orders[0]['created_at'] ?? null;
            $lastFormatted = $lastCreatedAt
                ? date('d.m.Y H:i', strtotime($lastCreatedAt))
                : '';
            ?>
            <div class="small text-muted text-md-end">
                <div class="fw-semibold mb-1">
                    <?= __('page.orders.stats.title'); ?>
                </div>
                <div>
                    <?= __('page.orders.stats.total_orders'); ?>
                    <strong><?= $totalOrders; ?></strong>
                </div>
                <div>
                    <?= __('page.orders.stats.total_amount'); ?>
                    <strong>
                        <?= number_format($totalAmount, 2, '.', ' '); ?>
                        <?= __('price.currency.uah', 'грн'); ?>
                    </strong>
                </div>
                <div>
                    <?= __('page.orders.stats.paid_count'); ?>
                    <strong><?= $paidCount; ?></strong>
                </div>
                <div class="text-muted mt-1">
                    <?= __('page.orders.stats.last_order'); ?>
                    <strong><?= htmlspecialchars($lastFormatted, ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- FLASH -->
    <?php if (!empty($flash)): ?>
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="<?= __('action.close'); ?>"></button>
        </div>
    <?php endif; ?>

    <?php if ($totalOrders === 0): ?>

        <!-- ПУСТИЙ СТАН -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="mb-3">
                    <div class="rounded-4 border border-dashed d-inline-flex align-items-center justify-content-center px-4 py-3">
                        <i class="bi bi-bag text-muted fs-2 me-2"></i>
                        <span class="text-muted fw-semibold">
                            <?= __('page.orders.empty.title'); ?>
                        </span>
                    </div>
                </div>
                <p class="text-muted mb-3">
                    <?= __('page.orders.empty.text'); ?>
                </p>
                <a href="/products" class="btn btn-warning text-dark fw-semibold px-4">
                    <i class="bi bi-grid-3x3-gap me-1"></i>
                    <?= __('page.orders.empty.button'); ?>
                </a>
            </div>
        </div>

    <?php else: ?>

        <!-- БЛОК ІСТОРІЇ У ВИГЛЯДІ ЛІНІЙКИ КАРТОК -->
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center">
                        <span class="border-start border-4 border-warning me-2" style="height:32px;"></span>
                        <div>
                            <div class="fw-semibold">
                                <?= __('page.orders.history.title'); ?>
                            </div>
                            <div class="small text-muted">
                                <?= __('page.orders.history.subtitle'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="small text-muted mt-2 mt-md-0">
                        <?= __('page.orders.history.support.question'); ?>
                        <?= __('page.orders.history.support.action_prefix'); ?>
                        <a href="/support" class="text-decoration-none fw-semibold">
                            <?= __('support.service'); ?>
                        </a>.
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">
                <!-- Лінійка / таймлайн -->
                <div class="position-relative mt-3">
                    <!-- Вертикальна лінія зліва (тільки на md+) -->
                    <div class="d-none d-md-block position-absolute top-0 bottom-0 start-0 ms-3"
                         style="width:2px;background:rgba(0,0,0,0.06);"></div>

                    <?php foreach ($orders as $index => $order): ?>
                        <?php
                        $statusRaw   = (string)($order['status_code'] ?? $order['status'] ?? '');
                        $status      = mb_strtolower($statusRaw);
                        $createdAt   = $order['created_at'] ?? '';
                        $createdAtFormatted = $createdAt
                            ? date('d.m.Y H:i', strtotime($createdAt))
                            : '';

                        // Колір та іконка статусу
                        $badgeClass = 'bg-secondary-subtle text-secondary';
                        $statusIcon = 'bi-hourglass';
                        switch ($status) {
                            case 'new':
                            case 'pending':
                            case 'очікує':
                                $badgeClass = 'bg-warning-subtle text-warning';
                                $statusIcon = 'bi-hourglass-split';
                                break;
                            case 'paid':
                            case 'completed':
                            case 'завершено':
                            case 'оплачено':
                                $badgeClass = 'bg-success-subtle text-success';
                                $statusIcon = 'bi-check-circle';
                                break;
                            case 'cancelled':
                            case 'canceled':
                            case 'відмінено':
                                $badgeClass = 'bg-danger-subtle text-danger';
                                $statusIcon = 'bi-x-circle';
                                break;
                            case 'processing':
                            case 'в обробці':
                                $badgeClass = 'bg-info-subtle text-info';
                                $statusIcon = 'bi-gear-wide-connected';
                                break;
                        }

                        // Номер замовлення
                        $orderNumber = $order['order_number'] ?? null;
                        $orderLabel  = $orderNumber !== null && $orderNumber !== ''
                            ? '№ ' . (string)$orderNumber
                            : '#' . (int)$order['id'];

                        // Кількість позицій
                        $itemsCount = (int)($order['total_products'] ?? $order['items_count'] ?? 0);

                        // Оплата / доставка / місто
                        $paymentMethod   = trim((string)($order['payment_method'] ?? $order['payment_title'] ?? ''));
                        $deliveryMethod  = trim((string)($order['delivery_method'] ?? $order['shipping_method'] ?? ''));
                        $deliveryCity    = trim((string)($order['shipping_city'] ?? $order['city'] ?? ''));
                        $paymentDeliveryParts = [];

                        if ($paymentMethod !== '') {
                            $paymentDeliveryParts[] = 'Оплата: ' . $paymentMethod;
                        }
                        if ($deliveryMethod !== '' || $deliveryCity !== '') {
                            $deliveryLabel = $deliveryMethod;
                            if ($deliveryCity !== '') {
                                $deliveryLabel .= ($deliveryLabel ? ' · ' : '') . $deliveryCity;
                            }
                            if ($deliveryLabel !== '') {
                                $paymentDeliveryParts[] = 'Доставка: ' . $deliveryLabel;
                            }
                        }

                        $paymentDeliveryText = implode(' | ', $paymentDeliveryParts);

                        // Сума
                        $total    = (float)($order['total_amount'] ?? $order['total'] ?? 0);
                        $currency = $order['currency'] ?? 'грн';

                        // Коментар клієнта / менеджера, якщо є
                        $customerComment = trim((string)($order['customer_comment'] ?? ''));
                        $managerComment  = trim((string)($order['manager_comment'] ?? ''));
                        ?>

                        <div class="d-flex position-relative mb-3">
                            <!-- Кружечок таймлайну -->
                            <div class="d-none d-md-flex flex-column align-items-center me-3">
                                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center"
                                     style="width:18px;height:18px; margin-top:16px;">
                                    <i class="bi bi-dot text-white"></i>
                                </div>
                            </div>

                            <!-- Картка замовлення -->
                            <div class="flex-grow-1">
                                <div class="card border-0 shadow-sm rounded-4 mb-2">
                                    <div class="card-body p-3 p-md-4">
                                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <div class="me-2">
                                                        <span class="badge <?= $badgeClass; ?> d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill">
                                                            <i class="bi <?= $statusIcon; ?>"></i>
                                                            <span>
                                                                <?= htmlspecialchars(
                                                                    $statusRaw !== '' ? $statusRaw : __('page.orders.status.not_specified'),
                                                                    ENT_QUOTES,
                                                                    'UTF-8'
                                                                ); ?>
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="small text-muted">
                                                        <i class="bi bi-calendar-event me-1"></i>
                                                        <?= htmlspecialchars($createdAtFormatted, ENT_QUOTES, 'UTF-8'); ?>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-wrap align-items-center small text-muted mb-2">
                                                    <span class="me-3">
                                                        <span class="text-dark fw-semibold">
                                                            <?= __('page.orders.label.order'); ?>
                                                        </span>
                                                        <?= htmlspecialchars($orderLabel, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                    <span class="me-3">
                                                        <i class="bi bi-hash me-1"></i>
                                                        <?= __('page.orders.label.id'); ?>
                                                        <?= (int)$order['id']; ?>
                                                    </span>
                                                    <?php if ($itemsCount > 0): ?>
                                                        <span class="me-3">
                                                            <i class="bi bi-box-seam me-1"></i>
                                                            <?= $itemsCount; ?> <?= __('page.orders.label.items_count'); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <?php if ($paymentDeliveryText !== ''): ?>
                                                    <div class="small text-muted mb-1">
                                                        <i class="bi bi-truck me-1"></i>
                                                        <?= htmlspecialchars($paymentDeliveryText, ENT_QUOTES, 'UTF-8'); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="small text-muted mb-1">
                                                        <i class="bi bi-truck me-1"></i>
                                                        <?= __('page.orders.label.delivery_details_pending'); ?>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if ($customerComment !== '' || $managerComment !== ''): ?>
                                                    <div class="mt-2">
                                                        <?php if ($customerComment !== ''): ?>
                                                            <div class="small text-muted">
                                                                <span class="fw-semibold text-dark">
                                                                    <?= __('page.orders.label.customer_comment'); ?>
                                                                </span>
                                                                «<?= htmlspecialchars($customerComment, ENT_QUOTES, 'UTF-8'); ?>»
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if ($managerComment !== ''): ?>
                                                            <div class="small text-muted mt-1">
                                                                <span class="fw-semibold text-dark">
                                                                    <?= __('page.orders.label.manager_comment'); ?>
                                                                </span>
                                                                «<?= htmlspecialchars($managerComment, ENT_QUOTES, 'UTF-8'); ?>»
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="text-md-end">
                                                <div class="small text-muted mb-1">
                                                    <?= __('page.orders.label.order_total'); ?>
                                                </div>
                                                <div class="h5 mb-1">
                                                    <?= number_format($total, 2, '.', ' '); ?>
                                                    <span class="h6 text-muted">
                                                        <?= htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                </div>
                                                <div class="small text-muted">
                                                    <?= __('page.orders.label.order_total_note'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card-footer bg-white border-0 small text-muted d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <span>
                    <?= __('page.orders.footer.support.prefix'); ?>
                    <a href="/support" class="text-decoration-none fw-semibold">
                        <?= __('support.service'); ?>
                    </a>.
                </span>
                <span class="mt-2 mt-md-0">
                    <?= __('page.orders.footer.status_update_note'); ?>
                </span>
            </div>
        </div>

    <?php endif; ?>

</section>
