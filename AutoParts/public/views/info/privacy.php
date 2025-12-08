<?php
/** @var string $pageTitle */
$title =  __('page.privacy.title') ?? $pageTitle;
?>

<section class="py-4 py-md-5">

    <!-- HERO-БЛОК -->
    <div class="mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="me-md-4 mb-3 mb-md-0">
                <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center"
                     style="width:72px;height:72px;">
                    <i class="bi bi-shield-lock fs-1 text-warning"></i>
                </div>
            </div>
            <div>
                <!-- менший, але все ще головний заголовок -->
                <h1 class="fw-bold fs-3 mb-2">
                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
                </h1>
                <p class="text-muted fs-6 mb-0">
                    <?= __('page.privacy.hero.text'); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- ОСНОВНИЙ КОНТЕНТ -->
        <div class="col-12 col-lg-8 d-flex flex-column gap-3">

            <!-- Які дані збираємо -->
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex">
                    <div class="me-3 flex-shrink-0">
                        <span class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                              style="width:40px;height:40px;">
                            <i class="bi bi-file-earmark-text fs-5 text-warning"></i>
                        </span>
                    </div>
                    <div>
                        <h2 class="fs-5 fw-semibold mb-2">
                            <?= __('page.privacy.section1.title'); ?>
                        </h2>
                        <p class="fs-6 mb-2">
                            <?= __('page.privacy.section1.intro'); ?>
                        </p>
                        <ul class="fs-6 mb-0 ps-3">
                            <li><?= __('page.privacy.section1.li1'); ?></li>
                            <li><?= __('page.privacy.section1.li2'); ?></li>
                            <li><?= __('page.privacy.section1.li3'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Як використовуємо -->
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex">
                    <div class="me-3 flex-shrink-0">
                        <span class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                              style="width:40px;height:40px;">
                            <i class="bi bi-gear-wide-connected fs-5 text-warning"></i>
                        </span>
                    </div>
                    <div>
                        <h2 class="fs-5 fw-semibold mb-2">
                            <?= __('page.privacy.section2.title'); ?>
                        </h2>
                        <p class="fs-6 mb-2">
                            <?= __('page.privacy.section2.intro'); ?>
                        </p>
                        <ul class="fs-6 mb-0 ps-3">
                            <li><?= __('page.privacy.section2.li1'); ?></li>
                            <li><?= __('page.privacy.section2.li2'); ?></li>
                            <li><?= __('page.privacy.section2.li3'); ?></li>
                            <li><?= __('page.privacy.section2.li4'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Як захищаємо -->
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex">
                    <div class="me-3 flex-shrink-0">
                        <span class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                              style="width:40px;height:40px;">
                            <i class="bi bi-lock fs-5 text-warning"></i>
                        </span>
                    </div>
                    <div>
                        <h2 class="fs-5 fw-semibold mb-2">
                            <?= __('page.privacy.section3.title'); ?>
                        </h2>
                        <p class="fs-6 mb-2">
                            <?= __('page.privacy.section3.intro'); ?>
                        </p>
                        <ul class="fs-6 mb-0 ps-3">
                            <li><?= __('page.privacy.section3.li1'); ?></li>
                            <li><?= __('page.privacy.section3.li2'); ?></li>
                            <li><?= __('page.privacy.section3.li3'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <!-- БОКОВИЙ БЛОК -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <span class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center me-2"
                              style="width:32px;height:32px;">
                            <i class="bi bi-info-circle text-warning"></i>
                        </span>
                        <h2 class="fs-6 fw-semibold mb-0 text-uppercase text-muted">
                            <?= __('page.privacy.sidebar.title'); ?>
                        </h2>
                    </div>
                    <ul class="fs-6 mb-0 ps-3">
                        <li><?= __('page.privacy.sidebar.li1'); ?></li>
                        <li><?= __('page.privacy.sidebar.li2'); ?></li>
                        <li><?= __('page.privacy.sidebar.li3'); ?></li>
                    </ul>
                </div>
            </div>

            <div class="alert alert-light border shadow-sm rounded-4 d-flex">
                <div class="me-2 flex-shrink-0">
                    <i class="bi bi-chat-dots text-warning fs-4"></i>
                </div>
                <div class="fs-6">
                    <div class="fw-semibold mb-1">
                        <?= __('page.privacy.contact.title'); ?>
                    </div>
                    <p class="mb-1">
                        <?= __('page.privacy.contact.text'); ?>
                        <a href="/support" class="fw-semibold text-decoration-none">
                            <?= __('page.privacy.contact.link'); ?>
                        </a>.
                    </p>
                </div>
            </div>
        </div>

    </div>

</section>
