<?php

use App\Core\Auth;

/**
 * @var array<string, mixed> $cart
 * @var array<int, array<string, mixed>> $deliveryMethods
 * @var array<int, array<string, mixed>> $paymentMethods
 */

// Поточний користувач (якщо залогінений)
$authUser = Auth::check() ? Auth::user() : null;

// Ім'я / ПІБ
$fullNameVal = '';
if ($authUser) {
    $fullNameVal =
        $authUser['name']
        ?? $authUser['full_name']
        ?? trim(($authUser['first_name'] ?? '') . ' ' . ($authUser['last_name'] ?? ''))
        ?? '';
}

// Телефон
$phoneVal = $authUser['phone'] ?? '';

// Адреса (якщо зберігаєш в профілі)
$addressVal = $authUser['address'] ?? '';

// ---------- КУПОН / ЗНИЖКА ----------
$cartSubtotal = (float)($cart['total'] ?? 0.0);

// купон може приїхати або з контролера ($cart['coupon']), або напряму з сесії
$coupon = $cart['coupon'] ?? ($_SESSION['cart_coupon'] ?? null);

$discount    = 0.0;
$couponCode  = '';
$couponName  = '';

if (is_array($coupon)) {
    $discount   = (float)($coupon['amount'] ?? 0);
    $couponCode = (string)($coupon['code'] ?? '');
    $couponName = (string)($coupon['name'] ?? '');
}

// якщо бекенд вже поклав готове поле 'discount' / 'total_with_discount' — поважаємо його
if (isset($cart['discount'])) {
    $discount = (float)$cart['discount'];
}

if ($discount < 0) {
    $discount = 0.0;
}
if ($discount > $cartSubtotal) {
    $discount = $cartSubtotal;
}

$grandTotal = isset($cart['total_with_discount'])
    ? (float)$cart['total_with_discount']
    : max(0.0, $cartSubtotal - $discount);

?>
<section class="py-4">

    <!-- HERO-БЛОК -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div class="mb-3 mb-md-0">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center me-2"
                     style="width:48px;height:48px;">
                    <i class="bi bi-cash-stack fs-4 text-warning"></i>
                </div>
                <div>
                    <h1 class="h4 h3-md fw-bold mb-1">
                        <?= __('checkout.title'); ?>
                    </h1>
                    <p class="text-muted small mb-0">
                        <?= __('checkout.subtitle'); ?>
                    </p>
                </div>
            </div>
            <div class="small text-muted">
                <?= __('checkout.step_2of2'); ?>
            </div>
        </div>

        <?php if (!empty($cart['items'])): ?>
            <div class="text-md-end small text-muted">
                <?= __('checkout.cart.items_in_cart'); ?>:
                <strong><?= count($cart['items']); ?></strong><br>
                <?php if ($discount > 0): ?>
                    <div>
                        <span class="d-block">
                            <?= __('checkout.cart.items_sum'); ?>:
                            <s>
                                <?= number_format($cartSubtotal, 2, '.', ' '); ?>
                                <?= __('common.currency.uah'); ?>
                            </s>
                        </span>
                        <span class="d-block">
                            <?= __('checkout.cart.total_with_coupon'); ?>:
                            <strong>
                                <?= number_format($grandTotal, 2, '.', ' '); ?>
                                <?= __('common.currency.uah'); ?>
                            </strong>
                        </span>
                        <?php if ($couponCode !== ''): ?>
                            <span class="d-block text-success">
                                <?= __('checkout.cart.coupon_label'); ?>:
                                <?= htmlspecialchars($couponCode, ENT_QUOTES, 'UTF-8'); ?>
                                (−<?= number_format($discount, 2, '.', ' '); ?> <?= __('common.currency.uah'); ?>)
                            </span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?= __('checkout.cart.sum_label'); ?>:
                    <strong>
                        <?= number_format($cartSubtotal, 2, '.', ' '); ?>
                        <?= __('common.currency.uah'); ?>
                    </strong>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="row g-4">

        <!-- ЛІВА КОЛОНКА: ФОРМА -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h2 class="h6 fw-semibold mb-0 d-flex align-items-center">
                        <i class="bi bi-person-lines-fill me-2 text-warning"></i>
                        <?= __('checkout.form.recipient_data_title'); ?>
                    </h2>
                </div>
                <div class="card-body fs-6">

                    <form action="/checkout/submit" method="post" class="row g-3">
                        <?= \App\Core\Csrf::csrfInput(); ?>

                        <!-- Контактні дані -->
                        <div class="col-12 col-md-6">
                            <label for="full_name" class="form-label">
                                <?= __('checkout.form.full_name.label'); ?>
                            </label>
                            <input
                                type="text"
                                name="full_name"
                                id="full_name"
                                class="form-control"
                                placeholder="<?= __('checkout.form.full_name.placeholder'); ?>"
                                value="<?= htmlspecialchars($fullNameVal, ENT_QUOTES, 'UTF-8'); ?>"
                                required>
                        </div>

                        <div class="col-12 col-md-6 mb-0">
                            <label for="phone" class="form-label">
                                <?= __('checkout.form.phone.label'); ?>
                            </label>
                            <input
                                type="tel"
                                name="phone"
                                id="phone"
                                class="form-control"
                                placeholder="<?= __('checkout.form.phone.placeholder'); ?>"
                                autocomplete="tel"
                                value="<?= htmlspecialchars($phoneVal, ENT_QUOTES, 'UTF-8'); ?>"
                                required>
                        </div>

                        <!-- Адреса доставки -->
                        <div class="col-12">
                            <label for="address" class="form-label">
                                <?= __('checkout.form.address.label'); ?>
                            </label>
                            <input
                                type="text"
                                name="address"
                                id="address"
                                class="form-control"
                                placeholder="<?= __('checkout.form.address.placeholder'); ?>"
                                value="<?= htmlspecialchars($addressVal, ENT_QUOTES, 'UTF-8'); ?>"
                                required>
                            <div class="form-text">
                                <?= __('checkout.form.address.help'); ?>
                            </div>
                        </div>

                        <!-- Спосіб доставки -->
                        <div class="col-12 col-md-6">
                            <label for="delivery_method_id" class="form-label">
                                <?= __('checkout.form.delivery_method.label'); ?>
                            </label>
                            <select
                                name="delivery_method_id"
                                id="delivery_method_id"
                                class="form-select"
                                required>
                                <?php foreach ($deliveryMethods as $method): ?>
                                    <option value="<?= (int)$method['id']; ?>">
                                        <?= htmlspecialchars((string)($method['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                <?= __('checkout.form.delivery_method.help'); ?>
                            </div>
                        </div>

                        <!-- Спосіб оплати -->
                        <div class="col-12 col-md-6">
                            <label for="payment_method_id" class="form-label">
                                <?= __('checkout.form.payment_method.label'); ?>
                            </label>
                            <select
                                name="payment_method_id"
                                id="payment_method_id"
                                class="form-select"
                                required>
                                <?php foreach ($paymentMethods as $method): ?>
                                    <option value="<?= (int)$method['id']; ?>">
                                        <?= htmlspecialchars((string)($method['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                <?= __('checkout.form.payment_method.help'); ?>
                            </div>
                        </div>

                        <!-- Примітки -->
                        <div class="col-12">
                            <label for="notes" class="form-label">
                                <?= __('checkout.form.notes.label'); ?>
                            </label>
                            <textarea
                                name="notes"
                                id="notes"
                                rows="3"
                                class="form-control"
                                placeholder="<?= __('checkout.form.notes.placeholder'); ?>"></textarea>
                        </div>

                        <!-- Підтвердження умов (опціонально, тільки фронт) -->
                        <div class="col-12">
                            <div class="form-check small">
                                <input class="form-check-input" type="checkbox" value="1" id="agree" checked>
                                <label class="form-check-label" for="agree">
                                    <?= __('checkout.form.agree.prefix'); ?>
                                    <a href="/info/payment-delivery" class="text-decoration-none">
                                        <?= __('checkout.form.agree.payment_link'); ?>
                                    </a>
                                    <?= ' ' . __('checkout.form.agree.and') . ' '; ?>
                                    <a href="/info/privacy" class="text-decoration-none">
                                        <?= __('checkout.form.agree.privacy_link'); ?>
                                    </a>.
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mt-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <a href="/cart" class="btn btn-link text-decoration-none mb-2 mb-md-0 p-0">
                                <i class="bi bi-arrow-left me-1"></i>
                                <?= __('checkout.form.back_to_cart'); ?>
                            </a>
                            <button type="submit" class="btn btn-warning text-dark fw-semibold px-4">
                                <?= __('checkout.form.submit'); ?>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- ПРАВА КОЛОНКА: ПІДСУМОК ЗАМОВЛЕННЯ -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-semibold mb-0 d-flex align-items-center">
                        <i class="bi bi-bag-check me-2 text-warning"></i>
                        <?= __('checkout.summary.title'); ?>
                    </h2>
                    <span class="badge rounded-pill text-bg-light small">
                        <?= count($cart['items']); ?>
                        <?= __('checkout.summary.items_badge'); ?>
                    </span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($cart['items'] as $item): ?>
                            <?php
                            $productName = htmlspecialchars((string)($item['product']['name'] ?? 'Товар'), ENT_QUOTES, 'UTF-8');
                            $qty         = (int)($item['quantity'] ?? 1);
                            $price       = (float)($item['price'] ?? 0);
                            $sum         = $price * $qty;
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="me-2">
                                    <div class="fw-semibold small"><?= $productName; ?></div>
                                    <div class="text-muted small">
                                        ×<?= $qty; ?> &middot;
                                        <?= number_format($price, 2, '.', ' '); ?>
                                        <?= __('common.currency.uah'); ?>
                                    </div>
                                </div>
                                <div class="fw-semibold small text-end">
                                    <?= number_format($sum, 2, '.', ' '); ?>
                                    <?= __('common.currency.uah'); ?>
                                </div>
                            </li>
                        <?php endforeach; ?>

                        <li class="list-group-item d-flex justify-content-between small">
                            <span class="text-muted"><?= __('checkout.summary.subtotal'); ?></span>
                            <span>
                                <?= number_format($cartSubtotal, 2, '.', ' '); ?>
                                <?= __('common.currency.uah'); ?>
                            </span>
                        </li>

                        <?php if ($discount > 0): ?>
                            <li class="list-group-item d-flex justify-content-between small text-success">
                                <span>
                                    <?= __('checkout.summary.coupon_discount'); ?>
                                    <?php if ($couponCode !== ''): ?>
                                        (<?= htmlspecialchars($couponCode, ENT_QUOTES, 'UTF-8'); ?>)
                                    <?php endif; ?>
                                </span>
                                <span>
                                    -<?= number_format($discount, 2, '.', ' '); ?>
                                    <?= __('common.currency.uah'); ?>
                                </span>
                            </li>
                        <?php endif; ?>

                        <li class="list-group-item d-flex justify-content-between small">
                            <span class="text-muted"><?= __('checkout.summary.delivery'); ?></span>
                            <span class="text-success">
                                <?= __('checkout.summary.delivery_tbd'); ?>
                            </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-bold"><?= __('checkout.summary.total'); ?></span>
                            <span class="fw-bold fs-5">
                                <?= number_format($grandTotal, 2, '.', ' '); ?>
                                <?= __('common.currency.uah'); ?>
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 small text-muted">
                    <?= __('checkout.summary.footer_note'); ?>
                </div>
            </div>
        </div>

    </div>

</section>
