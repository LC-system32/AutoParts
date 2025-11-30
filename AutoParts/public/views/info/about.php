<?php
/** @var string $pageTitle */
$title = __('page.about.title') ?? $pageTitle;
?>

<section class="py-4 py-md-5">

    <!-- HERO-БЛОК -->
    <div class="mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="me-md-4 mb-3 mb-md-0">
                <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center"
                     style="width:72px;height:72px;">
                    <i class="bi bi-gear-wide-connected fs-1 text-warning"></i>
                </div>
            </div>
            <div>
                <h1 class="fw-bold fs-3 mb-2">
                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
                </h1>
                <p class="text-muted fs-6 mb-0">
                    <?= __('page.about.hero.subtitle'); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Хто ми -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 fs-6">
                    <h2 class="fs-5 fw-semibold mb-3"><?= __('page.about.who_we_are.title'); ?></h2>
                    <p class="mb-2">
                        <?= __('page.about.who_we_are.p1'); ?>
                    </p>
                    <p class="mb-0 text-muted">
                        <?= __('page.about.who_we_are.p2'); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Наша місія -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 fs-6">
                    <h2 class="fs-5 fw-semibold mb-3"><?= __('page.about.mission.title'); ?></h2>
                    <p class="mb-2">
                        <?= __('page.about.mission.p1'); ?>
                    </p>
                    <p class="mb-0 text-muted">
                        <?= __('page.about.mission.p2'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- НАШІ ПЕРЕВАГИ -->
    <h2 class="fs-5 fw-semibold mb-3"><?= __('page.about.benefits.title'); ?></h2>
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex fs-6">
                    <div class="me-3 flex-shrink-0 d-flex align-items-start">
                        <i class="bi bi-box-seam fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1"><?= __('page.about.benefit1.title'); ?></div>
                        <p class="text-muted mb-0">
                            <?= __('page.about.benefit1.text'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex fs-6">
                    <div class="me-3 flex-shrink-0 d-flex align-items-start">
                        <i class="bi bi-tag fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1"><?= __('page.about.benefit2.title'); ?></div>
                        <p class="text-muted mb-0">
                            <?= __('page.about.benefit2.text'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex fs-6">
                    <div class="me-3 flex-shrink-0 d-flex align-items-start">
                        <i class="bi bi-headset fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1"><?= __('page.about.benefit3.title'); ?></div>
                        <p class="text-muted mb-0">
                            <?= __('page.about.benefit3.text'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex fs-6">
                    <div class="me-3 flex-shrink-0 d-flex align-items-start">
                        <i class="bi bi-truck fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="fw-semibold mb-1"><?= __('page.about.benefit4.title'); ?></div>
                        <p class="text-muted mb-0">
                            <?= __('page.about.benefit4.text'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- БЛОК ДОВІРИ / ЗАВЕРШАЛЬНИЙ -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center fs-6">
            <div class="me-md-3 mb-3 mb-md-0 d-flex align-items-center">
                <i class="bi bi-heart-fill text-warning fs-3 me-2"></i>
                <span class="fw-semibold">
                    <?= __('page.about.trust.title'); ?>
                </span>
            </div>
            <p class="mb-0 text-muted">
                <?= __('page.about.trust.text'); ?>
            </p>
        </div>
    </div>

</section>
