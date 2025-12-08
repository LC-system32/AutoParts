<?php
/**
 * Контактна інформація – розширений варіант
 */
?>

<section class="py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-11">

            <!-- HERO-БЛОК (вирівняний по шрифтам) -->
            <div class="mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center">
                    <div class="me-md-4 mb-3 mb-md-0">
                        <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center"
                             style="width:80px;height:80px;">
                            <i class="bi bi-geo-alt fs-1 text-warning"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="fw-bold fs-3 mb-2">
                            <?= __('page.contacts.title'); ?>
                        </h1>
                        <p class="text-muted fs-6 mb-0">
                            <?= __('page.contacts.hero.subtitle'); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- ПЕРШИЙ РЯД: 3 КАРТКИ -->
            <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">

                <!-- Адреса офісу -->
                <div class="col">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body p-4 fs-6">
                            <div class="d-flex align-items-center mb-3">
                                <span class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                      style="width:48px;height:48px;">
                                    <i class="bi bi-building fs-4 text-warning"></i>
                                </span>
                                <h2 class="fs-5 fw-semibold mb-0">
                                    <?= __('page.contacts.office.title'); ?>
                                </h2>
                            </div>
                            <p class="mb-1">
                                <?= __('page.contacts.office.address_line1'); ?>
                            </p>
                            <p class="mb-3">
                                <?= __('page.contacts.office.address_line2'); ?>
                            </p>
                            <p class="text-muted mb-0">
                                <?= __('page.contacts.office.note'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Телефони -->
                <div class="col">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body p-4 fs-6">
                            <div class="d-flex align-items-center mb-3">
                                <span class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                      style="width:48px;height:48px;">
                                    <i class="bi bi-telephone fs-4 text-warning"></i>
                                </span>
                                <h2 class="fs-5 fw-semibold mb-0">
                                    <?= __('page.contacts.phones.title'); ?>
                                </h2>
                            </div>
                            <p class="mb-2">
                                <i class="bi bi-telephone me-2"></i>
                                <strong>+38 (044) 123-45-67</strong><br>
                                <span class="text-muted">
                                    <?= __('page.contacts.phones.main_label'); ?>
                                </span>
                            </p>
                            <p class="mb-3">
                                <i class="bi bi-phone me-2"></i>
                                <strong>+38 (067) 890-12-34</strong><br>
                                <span class="text-muted">
                                    <?= __('page.contacts.phones.mobile_label'); ?>
                                </span>
                            </p>
                            <p class="text-muted mb-0">
                                <?= __('page.contacts.phones.note'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Email + графік роботи -->
                <div class="col">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body p-4 fs-6">
                            <div class="d-flex align-items-center mb-3">
                                <span class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                      style="width:48px;height:48px;">
                                    <i class="bi bi-envelope fs-4 text-warning"></i>
                                </span>
                                <h2 class="fs-5 fw-semibold mb-0">
                                    <?= __('page.contacts.email_schedule.title'); ?>
                                </h2>
                            </div>
                            <p class="mb-3">
                                <i class="bi bi-envelope-open me-2"></i>
                                <strong>support@autoparts.ua</strong><br>
                                <span class="text-muted">
                                    <?= __('page.contacts.email.label'); ?>
                                </span>
                            </p>
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="bi bi-clock-history fs-4 text-warning"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold mb-1">
                                        <?= __('page.contacts.schedule.title'); ?>
                                    </div>
                                    <p class="mb-1">
                                        <?= __('page.contacts.schedule.weekdays'); ?>
                                    </p>
                                    <p class="mb-1">
                                        <?= __('page.contacts.schedule.saturday'); ?>
                                    </p>
                                    <p class="mb-0 text-muted">
                                        <?= __('page.contacts.schedule.sunday'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ДРУГИЙ РЯД: ОНЛАЙН + ЯК НАС ЗНАЙТИ -->
            <div class="row g-4">
                <!-- Зв’язатися онлайн -->
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex fs-6">
                            <div class="me-3 flex-shrink-0">
                                <span class="rounded-circle bg-warning bg-opacity-15 d-flex align-items-center justify-content-center"
                                      style="width:48px;height:48px;">
                                    <i class="bi bi-chat-dots fs-4"></i>
                                </span>
                            </div>
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2 fs-6">
                                    <?= __('page.contacts.online_support.badge'); ?>
                                </div>
                                <p class="mb-2">
                                    <?= __('page.contacts.online_support.p1'); ?>
                                </p>
                                <p class="mb-3 text-muted">
                                    <?= __('page.contacts.online_support.p2'); ?>
                                </p>
                                <a href="/support" class="btn btn-warning text-dark fw-semibold">
                                    <?= __('page.contacts.online_support.button'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Як нас знайти -->
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 fs-6">
                            <div class="d-flex align-items-center mb-2">
                                <span class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center me-2"
                                      style="width:40px;height:40px;">
                                    <i class="bi bi-map fs-5 text-warning"></i>
                                </span>
                                <div class="fw-semibold">
                                    <?= __('page.contacts.find_us.title'); ?>
                                </div>
                            </div>
                            <p class="text-muted mb-2">
                                <?= __('page.contacts.find_us.p1'); ?>
                            </p>
                            <p class="text-muted mb-0">
                                <?= __('page.contacts.find_us.p2'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
