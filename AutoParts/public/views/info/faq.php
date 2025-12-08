<?php
/** @var string $pageTitle */
$title =  __('page.faq.title') ?? $pageTitle;
?>

<section class="py-4 py-md-5">

    <!-- HERO-БЛОК -->
    <div class="mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="me-md-4 mb-3 mb-md-0">
                <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center"
                     style="width:72px;height:72px;">
                    <i class="bi bi-question-circle fs-1 text-warning"></i>
                </div>
            </div>
            <div>
                <h1 class="fw-bold fs-3 mb-2">
                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
                </h1>
                <p class="text-muted fs-6 mb-0">
                    <?= __('page.faq.hero.subtitle'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- КОРОТКІ ОГЛЯДОВІ КАРТКИ -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex fs-6">
                    <div class="me-3 flex-shrink-0">
                        <span class="rounded-circle bg-warning bg-opacity-15 d-flex align-items-center justify-content-center"
                              style="width:40px;height:40px;">
                            <i class="bi bi-cart-check fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1">
                            <?= __('page.faq.overview.order.title'); ?>
                        </div>
                        <p class="text-muted mb-0">
                            <?= __('page.faq.overview.order.text'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex fs-6">
                    <div class="me-3 flex-shrink-0">
                        <span class="rounded-circle bg-warning bg-opacity-15 d-flex align-items-center justify-content-center"
                              style="width:40px;height:40px;">
                            <i class="bi bi-shield-lock fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1">
                            <?= __('page.faq.overview.payment.title'); ?>
                        </div>
                        <p class="text-muted mb-0">
                            <?= __('page.faq.overview.payment.text'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex fs-6">
                    <div class="me-3 flex-shrink-0">
                        <span class="rounded-circle bg-warning bg-opacity-15 d-flex align-items-center justify-content-center"
                              style="width:40px;height:40px;">
                            <i class="bi bi-truck fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1">
                            <?= __('page.faq.overview.delivery.title'); ?>
                        </div>
                        <p class="text-muted mb-0">
                            <?= __('page.faq.overview.delivery.text'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ОСНОВНИЙ FAQ -->
    <div class="row">
        <div class="col-12 col-lg-9 col-xl-8 mx-auto">

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 p-md-4">

                    <!-- flush робить акордеон легшим, а не "шкафом" -->
                    <div class="accordion accordion-flush" id="faqAccordion">

                        <!-- 1. Як зробити замовлення? -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOrder">
                                <button class="accordion-button collapsed fs-6" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseOrder"
                                        aria-expanded="false"
                                        aria-controls="collapseOrder">
                                    <?= __('page.faq.q1.question'); ?>
                                </button>
                            </h2>
                            <div id="collapseOrder"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingOrder"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body fs-6">
                                    <?= __('page.faq.q1.answer'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Реєстрація -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingAccount">
                                <button class="accordion-button collapsed fs-6" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseAccount"
                                        aria-expanded="false"
                                        aria-controls="collapseAccount">
                                    <?= __('page.faq.q2.question'); ?>
                                </button>
                            </h2>
                            <div id="collapseAccount"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingAccount"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body fs-6">
                                    <?= __('page.faq.q2.answer'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Сумісність -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCompatibility">
                                <button class="accordion-button collapsed fs-6" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseCompatibility"
                                        aria-expanded="false"
                                        aria-controls="collapseCompatibility">
                                    <?= __('page.faq.q3.question'); ?>
                                </button>
                            </h2>
                            <div id="collapseCompatibility"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingCompatibility"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body fs-6">
                                    <?= __('page.faq.q3.answer'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Оплата -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingPayment">
                                <button class="accordion-button collapsed fs-6" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapsePayment"
                                        aria-expanded="false"
                                        aria-controls="collapsePayment">
                                    <?= __('page.faq.q4.question'); ?>
                                </button>
                            </h2>
                            <div id="collapsePayment"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingPayment"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body fs-6">
                                    <?= __('page.faq.q4.answer'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Безпека -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSecurity">
                                <button class="accordion-button collapsed fs-6" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseSecurity"
                                        aria-expanded="false"
                                        aria-controls="collapseSecurity">
                                    <?= __('page.faq.q5.question'); ?>
                                </button>
                            </h2>
                            <div id="collapseSecurity"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingSecurity"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body fs-6">
                                    <?= __('page.faq.q5.answer'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Доставка -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDelivery">
                                <button class="accordion-button collapsed fs-6" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseDelivery"
                                        aria-expanded="false"
                                        aria-controls="collapseDelivery">
                                    <?= __('page.faq.q6.question'); ?>
                                </button>
                            </h2>
                            <div id="collapseDelivery"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingDelivery"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body fs-6">
                                    <?= __('page.faq.q6.answer'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- 7. Відстеження -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTrack">
                                <button class="accordion-button collapsed fs-6" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseTrack"
                                        aria-expanded="false"
                                        aria-controls="collapseTrack">
                                    <?= __('page.faq.q7.question'); ?>
                                </button>
                            </h2>
                            <div id="collapseTrack"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingTrack"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body fs-6">
                                    <?= __('page.faq.q7.answer'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- 8. Повернення -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingReturn">
                                <button class="accordion-button collapsed fs-6" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseReturn"
                                        aria-expanded="false"
                                        aria-controls="collapseReturn">
                                    <?= __('page.faq.q8.question'); ?>
                                </button>
                            </h2>
                            <div id="collapseReturn"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingReturn"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body fs-6">
                                    <?= __('page.faq.q8.answer'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- 9. Пошкоджений товар -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDamaged">
                                <button class="accordion-button collapsed fs-6" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseDamaged"
                                        aria-expanded="false"
                                        aria-controls="collapseDamaged">
                                    <?= __('page.faq.q9.question'); ?>
                                </button>
                            </h2>
                            <div id="collapseDamaged"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingDamaged"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body fs-6">
                                    <?= __('page.faq.q9.answer'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- 10. Зв'язок з підтримкою -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSupport">
                                <button class="accordion-button collapsed fs-6" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseSupport"
                                        aria-expanded="false"
                                        aria-controls="collapseSupport">
                                    <?= __('page.faq.q10.question'); ?>
                                </button>
                            </h2>
                            <div id="collapseSupport"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingSupport"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body fs-6">
                                    <?= __('page.faq.q10.answer'); ?>
                                </div>
                            </div>
                        </div>

                    </div><!-- /accordion -->

                </div>
            </div>

        </div>
    </div>

</section>
