<?php

/**
 * @var string      $pageTitle
 * @var int         $orderId
 * @var bool        $isSuccess
 * @var string|null $status
 * @var float|null  $amount
 * @var string|null $currency
 * @var string|null $paymentId
 * @var array|null  $liqpayResponse
 */

$amountFormatted = $amount !== null
    ? number_format($amount, 2, '.', ' ')
    : '—';

$currency = $currency ?? 'UAH';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5">

                        <?php if ($isSuccess): ?>
                            <div class="mb-3">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10"
                                    style="width:72px;height:72px;">
                                    <i class="bi bi-check2-circle fs-1 text-success"></i>
                                </div>
                            </div>
                            <h1 class="h4 fw-bold mb-2">
                                Оплату успішно завершено
                            </h1>
                            <p class="text-muted mb-4">
                                Ваше замовлення
                                <?php if ($orderId): ?>
                                    <strong>#<?= htmlspecialchars((string)$orderId, ENT_QUOTES, 'UTF-8'); ?></strong>
                                <?php else: ?>
                                    <strong>без номера</strong>
                                <?php endif; ?>
                                успішно оплачено.
                            </p>
                        <?php else: ?>
                            <div class="mb-3">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10"
                                    style="width:72px;height:72px;">
                                    <i class="bi bi-x-circle fs-1 text-danger"></i>
                                </div>
                            </div>
                            <h1 class="h4 fw-bold mb-2">
                                Оплату не вдалося завершити
                            </h1>
                            <p class="text-muted mb-4">
                                Сталася помилка під час проведення оплати.
                                Спробуйте ще раз або виберіть інший спосіб оплати.
                            </p>
                        <?php endif; ?>

                        <div class="row text-start justify-content-center mb-4">
                            <div class="col-12 col-md-8">
                                <ul class="list-group list-group-flush small">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="text-muted">Номер замовлення</span>
                                        <span>
                                            <?= $orderId
                                                ? '#' . htmlspecialchars((string)$orderId, ENT_QUOTES, 'UTF-8')
                                                : '—'; ?>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="text-muted">Сума</span>
                                        <span><?= $amountFormatted . ' ' . htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="text-muted">Статус LiqPay</span>
                                        <span><?= htmlspecialchars((string)($status ?? 'невідомий'), ENT_QUOTES, 'UTF-8'); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="text-muted">ID транзакції</span>
                                        <span>
                                            <?= $paymentId
                                                ? htmlspecialchars((string)$paymentId, ENT_QUOTES, 'UTF-8')
                                                : '—'; ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-md-row justify-content-center gap-2">
                            <a href="/" class="btn btn-warning text-dark fw-semibold px-4">
                                На головну
                            </a>
                            <a href="/orders" class="btn btn-outline-secondary px-4">
                                Мої замовлення
                            </a>
                        </div>

                    </div>

                    <?php if ($liqpayResponse): ?>
                        <div class="card-footer bg-light small text-muted text-start">
                            <details>
                                <summary class="mb-1">Технічна інформація LiqPay (debug)</summary>
                                <pre class="mb-0 mt-2" style="white-space: pre-wrap; word-break: break-all; font-size: .8rem;">
<?= htmlspecialchars(json_encode($liqpayResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>
                                </pre>
                            </details>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</section>