<?php
/** @var array<string,mixed> $user */
/** @var string|null $flash */
/** @var string|null $csrf */

$user = $user ?? [];
?>
<div class="container py-4">
    <h1 class="mb-4">
        <?= htmlspecialchars(__('page.profile.edit.title', 'Редагувати профіль'), ENT_QUOTES, 'UTF-8'); ?>
    </h1>

    <?php if (!empty($flash)): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form action="/profile/update" method="post" class="row g-3">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)$csrf, ENT_QUOTES, 'UTF-8'); ?>">

        <div class="col-md-6">
            <label for="first_name" class="form-label">
                <?= htmlspecialchars(__('page.profile.form.first_name.label', 'Ім’я'), ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <input
                type="text"
                class="form-control"
                id="first_name"
                name="first_name"
                value="<?= htmlspecialchars((string)($user['first_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <div class="col-md-6">
            <label for="last_name" class="form-label">
                <?= htmlspecialchars(__('page.profile.form.last_name.label', 'Прізвище'), ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <input
                type="text"
                class="form-control"
                id="last_name"
                name="last_name"
                value="<?= htmlspecialchars((string)($user['last_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label">
                <?= htmlspecialchars(__('page.profile.form.email.label', 'Email'), ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                value="<?= htmlspecialchars((string)($user['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <div class="col-md-6">
            <label for="phone" class="form-label">
                <?= htmlspecialchars(__('page.profile.form.phone.label', 'Телефон'), ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <input
                type="text"
                class="form-control"
                id="phone"
                name="phone"
                value="<?= htmlspecialchars((string)($user['phone'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
            >
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <?= htmlspecialchars(__('page.profile.form.submit', 'Зберегти зміни'), ENT_QUOTES, 'UTF-8'); ?>
            </button>
            <a href="/profile" class="btn btn-secondary ms-2">
                <?= htmlspecialchars(__('ui.cancel', 'Скасувати'), ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </div>
    </form>
</div>
