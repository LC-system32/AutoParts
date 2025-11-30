<?php

/** @var string|null $flash */
?>

<section class="py-3">
    <div class="container">
        <div class="row justify-content-center">
            <!-- Ліва колонка (опціонально можна додати картинку/текст пізніше) -->
            <div class="col-12 col-md-8 col-lg-6 col-xl-4">

                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <!-- Верхній блок з іконкою та заголовком -->
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center mb-3"
                            style="width:56px;height:56px;">
                            <i class="bi bi-person-lock fs-3 text-dark"></i>
                        </div>
                        <h1 class="h4 fw-bold mb-1">
                            <?= __('auth.login.title'); ?>
                        </h1>
                        <p class="text-muted small mb-0">
                            <?= __('auth.login.lead'); ?>
                        </p>
                    </div>

                    <!-- Flash-повідомлення -->
                    <?php if (!empty($flash)): ?>
                        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                            <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="<?= __('common.close'); ?>"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Форма входу -->
                    <form action="/login" method="post" class="mb-3">
                        <?= \App\Core\Csrf::csrfInput(); ?>

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-semibold text-uppercase text-muted">
                                <?= __('auth.login.email.label'); ?>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email"
                                    name="email"
                                    id="email"
                                    class="form-control border-start-0"
                                    placeholder="<?= __('auth.login.email.placeholder'); ?>"
                                    required>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="password" class="form-label small fw-semibold text-uppercase text-muted">
                                <?= __('auth.login.password.label'); ?>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password"
                                    name="password"
                                    id="password"
                                    class="form-control border-start-0"
                                    placeholder="<?= __('auth.login.password.placeholder'); ?>"
                                    required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check small">
                                <input class="form-check-input"
                                    type="checkbox"
                                    value="1"
                                    id="rememberMe">
                                <label class="form-check-label" for="rememberMe">
                                    <?= __('auth.login.remember_me'); ?>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 text-dark fw-semibold py-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            <?= __('auth.login.submit'); ?>
                        </button>
                    </form>

                    <p class="mt-3 mb-0 small text-center">
                        <?= __('auth.login.no_account'); ?>
                        <a href="/register" class="fw-semibold text-decoration-none">
                            <?= __('auth.login.register_link'); ?>
                        </a>
                    </p>
                    <a href="/auth/google?lang=<?= htmlspecialchars($currentLang ?? 'uk', ENT_QUOTES); ?>"
                        class="btn btn-outline-danger w-100 mb-2">
                        <i class="bi bi-google me-1"></i>
                        Увійти через Google
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>