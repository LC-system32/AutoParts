<?php
/** @var string|null $flash */
?>

<section class="py-3">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5 col-xl-4">

                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <!-- Верхній блок з іконкою та заголовком -->
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center mb-3"
                             style="width:56px;height:56px;">
                            <i class="bi bi-person-plus fs-3 text-dark"></i>
                        </div>
                        <h1 class="h4 fw-bold mb-1">
                            <?= __('auth.register.title'); ?>
                        </h1>
                        <p class="text-muted small mb-0">
                            <?= __('auth.register.lead'); ?>
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

                    <!-- Форма реєстрації -->
                    <form action="/register" method="post" class="mb-3">
                        <?= \App\Core\Csrf::csrfInput(); ?>

                        <div class="mb-3">
                            <label for="name" class="form-label small fw-semibold text-uppercase text-muted">
                                <?= __('auth.register.name.label'); ?>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       class="form-control border-start-0"
                                       placeholder="<?= __('auth.register.name.placeholder'); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-semibold text-uppercase text-muted">
                                <?= __('auth.register.email.label'); ?>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       class="form-control border-start-0"
                                       placeholder="<?= __('auth.register.email.placeholder'); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label small fw-semibold text-uppercase text-muted">
                                <?= __('auth.register.password.label'); ?>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password"
                                       name="password"
                                       id="password"
                                       class="form-control border-start-0"
                                       placeholder="<?= __('auth.register.password.placeholder'); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label small fw-semibold text-uppercase text-muted">
                                <?= __('auth.register.password_confirm.label'); ?>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password"
                                       name="password_confirm"
                                       id="password_confirm"
                                       class="form-control border-start-0"
                                       placeholder="<?= __('auth.register.password_confirm.placeholder'); ?>"
                                       required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 text-dark fw-semibold py-2">
                            <i class="bi bi-check2-circle me-1"></i>
                            <?= __('auth.register.submit'); ?>
                        </button>
                    </form>

                    <p class="mt-3 mb-0 small text-center">
                        <?= __('auth.register.have_account'); ?>
                        <a href="/login" class="fw-semibold text-decoration-none">
                            <?= __('auth.register.login_link'); ?>
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</section>
