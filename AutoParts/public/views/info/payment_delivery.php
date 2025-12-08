<?php
/** @var string $pageTitle */
$title = __('page.payment_delivery.title') ?? $pageTitle ;
?>

<section class="py-4 py-md-5">

    <!-- HERO-БЛОК -->
    <div class="mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="me-md-4 mb-3 mb-md-0">
                <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center mb-3"
                     style="width:72px;height:72px;">
                    <i class="bi bi-truck fs-1 text-warning"></i>
                </div>
            </div>
            <div>
                <h1 class="fw-bold fs-3 mb-2">
                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
                </h1>
                <p class="text-muted fs-6 mb-0">
                    <?= __('page.payment_delivery.hero.subtitle'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- ДВІ ОСНОВНІ КАРТКИ -->
    <div class="row g-4 align-items-stretch">

        <!-- Картка: Способи оплати -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-header bg-warning bg-opacity-10 border-0 rounded-top-4 py-3">
                    <h2 class="fs-5 fw-semibold mb-0 d-flex align-items-center">
                        <span class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center me-2"
                              style="width:32px;height:32px;">
                            <i class="bi bi-wallet2 text-warning"></i>
                        </span>
                        <?= __('page.payment_delivery.payment.title'); ?>
                    </h2>
                </div>
                <div class="card-body fs-6">

                    <div class="d-flex mb-3">
                        <div class="me-3 flex-shrink-0">
                            <i class="bi bi-credit-card-2-front text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-semibold mb-1">
                                <?= __('page.payment_delivery.payment.card1.title'); ?>
                            </div>
                            <div class="text-muted">
                                <?= __('page.payment_delivery.payment.card1.text'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <div class="me-3 flex-shrink-0">
                            <i class="bi bi-building text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-semibold mb-1">
                                <?= __('page.payment_delivery.payment.card2.title'); ?>
                            </div>
                            <div class="text-muted">
                                <?= __('page.payment_delivery.payment.card2.text'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="me-3 flex-shrink-0">
                            <i class="bi bi-box-seam text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-semibold mb-1">
                                <?= __('page.payment_delivery.payment.card3.title'); ?>
                            </div>
                            <div class="text-muted">
                                <?= __('page.payment_delivery.payment.card3.text'); ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Картка: Доставка -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-header bg-warning bg-opacity-10 border-0 rounded-top-4 py-3">
                    <h2 class="fs-5 fw-semibold mb-0 d-flex align-items-center">
                        <span class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center me-2"
                              style="width:32px;height:32px;">
                            <i class="bi bi-truck-front text-warning"></i>
                        </span>
                        <?= __('page.payment_delivery.delivery.title'); ?>
                    </h2>
                </div>
                <div class="card-body fs-6">

                    <div class="d-flex mb-3">
                        <div class="me-3 flex-shrink-0">
                            <i class="bi bi-geo-alt text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-semibold mb-1">
                                <?= __('page.payment_delivery.delivery.card1.title'); ?>
                            </div>
                            <div class="text-muted">
                                <?= __('page.payment_delivery.delivery.card1.text'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <div class="me-3 flex-shrink-0">
                            <i class="bi bi-clock-history text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-semibold mb-1">
                                <?= __('page.payment_delivery.delivery.card2.title'); ?>
                            </div>
                            <div class="text-muted">
                                <?= __('page.payment_delivery.delivery.card2.text'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="me-3 flex-shrink-0">
                            <i class="bi bi-cash-stack text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-semibold mb-1">
                                <?= __('page.payment_delivery.delivery.card3.title'); ?>
                            </div>
                            <div class="text-muted">
                                <?= __('page.payment_delivery.delivery.card3.text'); ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- ДОДАТКОВІ ІНФО-БЛОКИ ВНИЗУ -->
    <div class="row g-3 mt-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex fs-6">
                    <div class="me-3 flex-shrink-0">
                        <span class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                              style="width:40px;height:40px;">
                            <i class="bi bi-shield-check text-success fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1">
                            <?= __('page.payment_delivery.extra.guarantee.title'); ?>
                        </div>
                        <div class="text-muted mb-0">
                            <?= __('page.payment_delivery.extra.guarantee.text'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="alert alert-light border shadow-sm rounded-4 h-100 d-flex align-items-center fs-6 mb-0">
                <i class="bi bi-info-circle me-2 text-warning fs-5"></i>
                <div>
                    <?= __('page.payment_delivery.extra.note.text'); ?>
                    <a href="/support" class="fw-semibold text-decoration-none">
                        <?= __('page.payment_delivery.extra.note.link'); ?>
                    </a>.
                </div>
            </div>
        </div>
    </div>

</section>
