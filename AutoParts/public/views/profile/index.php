<?php

/** @var array<string, mixed> $user */
/** @var string|null $flash */
/** @var string|null $currentProfilePage */

// Адреса може бути або рядком, або вкладеним масивом (якщо ти так зробиш у майбутньому)
$addressData = $user['address'] ?? null;

// Телефон: спочатку беремо з акаунта, якщо нема – з адреси (якщо там є)
$phone = $user['phone'] ?? '';
if ($phone === '' && is_array($addressData)) {
    $phone = $addressData['phone'] ?? '';
}

// Повне ім’я для відображення
$displayName = $user['name']
    ?? $user['full_name']
    ?? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))
    ?: ($user['login'] ?? $user['email'] ?? __('page.profile.fallback_name', 'Користувач'));

// Ролі
$roles = [];
if (!empty($user['roles']) && is_array($user['roles'])) {
    $roles = $user['roles'];
}

// Ім’я / прізвище окремо для форми
$firstName = (string)($user['first_name'] ?? '');
$lastName  = (string)($user['last_name']  ?? '');
// Якщо в БД ще пусто, але є displayName – підставимо його в ім’я
if ($firstName === '' && $lastName === '' && $displayName !== '') {
    $firstName = $displayName;
}
// Складаємо повну адресу в один рядок
$fullAddress = '';
if (is_array($addressData) && !empty($addressData)) {
    $parts = [];

    if (!empty($addressData['country'])) {
        $parts[] = $addressData['country'];
    }
    if (!empty($addressData['region'])) {
        $parts[] = $addressData['region'];
    }
    if (!empty($addressData['city'])) {
        $parts[] = $addressData['city'];
    }
    if (!empty($addressData['street_address'])) {
        $parts[] = $addressData['street_address'];
    }
    if (!empty($addressData['postal_code'])) {
        $parts[] = $addressData['postal_code'];
    }

    $fullAddress = implode(', ', $parts);
} elseif (!empty($user['address'] ?? '')) {
    // Якщо адреса просто рядком (як у твоєму прикладі)
    $fullAddress = (string)$user['address'];
}

$currentProfilePage = $currentProfilePage ?? 'overview';

?>

<section class="py-4 ">

    <!-- ОБГОРТКА -->
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <!-- HERO-ПРОФІЛЮ -->
            <div class="d-flex flex-column flex-md-row align-items-md-center mb-4">
                <div class="me-md-3 mb-3 mb-md-0">
                    <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center"
                         style="width:72px;height:72px;">
                        <i class="bi bi-person-circle fs-1 text-warning"></i>
                    </div>
                </div>
                <div>
                    <h1 class="fw-bold fs-3 mb-1">
                        <?= htmlspecialchars(__('page.profile.title'), ENT_QUOTES, 'UTF-8'); ?>
                    </h1>
                    <p class="text-muted mb-0 fs-6">
                        <?= __('page.profile.subtitle'); ?>
                    </p>
                </div>
            </div>

            <?php if (!empty($flash)): ?>
                <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                    <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="<?= htmlspecialchars(__('ui.close', 'Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- ЛІВА КОЛОНКА: КАРТКА ПРОФІЛЮ + МЕНЮ -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-body p-4 fs-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3"
                                     style="width:56px;height:56px;">
                                    <i class="bi bi-person fs-3 text-warning"></i>
                                </div>
                                <div>
                                    <div class="small text-muted">
                                        <?= __('page.profile.card.caption'); ?>
                                    </div>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small mb-1">
                                    <?= __('page.profile.card.email'); ?>
                                </div>
                                <div>
                                    <?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>

                            <?php if ($phone !== ''): ?>
                                <div class="mb-3">
                                    <div class="text-muted small mb-1">
                                        <?= __('page.profile.card.phone'); ?>
                                    </div>
                                    <div>
                                        <?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($fullAddress !== ''): ?>
                                <div class="mb-3">
                                    <div class="text-muted small mb-1">
                                        <?= __('page.profile.card.main_address'); ?>
                                    </div>
                                    <div>
                                        <?= htmlspecialchars($fullAddress, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($roles)): ?>
                                <div class="mb-3">
                                    <div class="text-muted small mb-1">
                                        <?= __('page.profile.card.roles'); ?>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($roles as $role): ?>
                                            <span class="badge bg-light text-dark border">
                                                <?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($phone !== '' && $fullAddress === ''): ?>
                                <p class="text-muted small mt-3 mb-0">
                                    <?= __('page.profile.card.fill_hint'); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- МЕНЮ КАБІНЕТУ -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-3">
                            <div class="small text-muted mb-2">
                                <?= __('profile.menu.section_title'); ?>
                            </div>
                            <ul class="nav nav-pills flex-column gap-1 small">
                                <li class="nav-item">
                                    <a href="/profile"
                                       class="nav-link d-flex align-items-center <?= $currentProfilePage === 'overview' ? 'active' : 'link-dark'; ?>">
                                        <i class="bi bi-person me-2"></i>
                                        <?= __('profile.menu.overview'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/profile/orders"
                                       class="nav-link d-flex align-items-center <?= $currentProfilePage === 'orders' ? 'active' : 'link-dark'; ?>">
                                        <i class="bi bi-receipt me-2"></i>
                                        <?= __('profile.menu.orders'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/profile/addresses"
                                       class="nav-link d-flex align-items-center <?= $currentProfilePage === 'addresses' ? 'active' : 'link-dark'; ?>">
                                        <i class="bi bi-geo-alt me-2"></i>
                                        <?= __('profile.menu.addresses'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/profile/wishlist"
                                       class="nav-link d-flex align-items-center <?= $currentProfilePage === 'wishlist' ? 'active' : 'link-dark'; ?>">
                                        <i class="bi bi-heart me-2"></i>
                                        <?= __('profile.menu.wishlist'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

                <!-- ПРАВА КОЛОНКА: ФОРМА РЕДАГУВАННЯ -->
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 fs-6">
                            <h2 class="fs-5 fw-semibold mb-3">
                                <?= __('page.profile.form.title'); ?>
                            </h2>

                            <form action="/profile/update" method="post" class="row g-3">
                                <?= \App\Core\Csrf::csrfInput(); ?>

                                <!-- ЛОГІН (тільки для перегляду) -->
                                <div class="col-12 col-md-6">
                                    <label for="login" class="form-label">
                                        <?= __('page.profile.form.login.label'); ?>
                                    </label>
                                    <input
                                        type="text"
                                        id="login"
                                        class="form-control"
                                        value="<?= htmlspecialchars($user['login'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        disabled>
                                    <div class="form-text">
                                        <?= __('page.profile.form.login.help'); ?>
                                    </div>
                                </div>

                                <!-- Ім’я -->
                                <div class="col-12 col-md-6">
                                    <label for="first_name" class="form-label">
                                        <?= __('page.profile.form.first_name.label'); ?>
                                    </label>
                                    <input
                                        type="text"
                                        name="first_name"
                                        id="first_name"
                                        class="form-control"
                                        value="<?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <!-- Прізвище -->
                                <div class="col-12 col-md-6">
                                    <label for="last_name" class="form-label">
                                        <?= __('page.profile.form.last_name.label'); ?>
                                    </label>
                                    <input
                                        type="text"
                                        name="last_name"
                                        id="last_name"
                                        class="form-control"
                                        value="<?= htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <!-- Email -->
                                <div class="col-12 col-md-6">
                                    <label for="email" class="form-label">
                                        <?= __('page.profile.form.email.label'); ?>
                                    </label>
                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        class="form-control"
                                        value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        required>
                                </div>

                                <!-- Телефон -->
                                <div class="col-12 col-md-6">
                                    <label for="phone" class="form-label">
                                        <?= __('page.profile.form.phone.label'); ?>
                                    </label>
                                    <input
                                        type="text"
                                        name="phone"
                                        id="phone"
                                        class="form-control"
                                        placeholder="<?= htmlspecialchars(__('page.profile.form.phone.placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                                        value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <!-- Адреса доставки -->
                                <div class="col-12">
                                    <label for="address" class="form-label">
                                        <?= __('page.profile.form.address.label'); ?>
                                    </label>
                                    <input
                                        type="text"
                                        name="address"
                                        id="address"
                                        class="form-control"
                                        placeholder="<?= htmlspecialchars(__('page.profile.form.address.placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                                        value="<?= htmlspecialchars($fullAddress, ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="col-12 mt-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning text-dark fw-semibold">
                                        <?= __('page.profile.form.submit'); ?>
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>
