<?php
/**
 * Редагування адреси
 *
 * Представлення для редагування наявної адреси доставки. Заповнює поля
 * значеннями з $address. Дозволяє змінити будь-який атрибут.
 *
 * @var array<string,mixed> $address
 * @var string $csrf
 * @var string|null $flash
 */

$address = $address ?? [];
?>
<div class="container py-4">
    <h1 class="mb-4">
        <?= htmlspecialchars(__('page.addresses.edit.title', 'Редагувати адресу'), ENT_QUOTES, 'UTF-8'); ?>
    </h1>

    <?php if (!empty($flash)): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form action="/profile/addresses/<?= (int)$address['id']; ?>/update" method="post" class="row g-3">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">

        <div class="col-md-6">
            <label for="full_name" class="form-label">
                <?= htmlspecialchars(__('page.addresses.new.form.full_name.label', 'ПІБ отримувача'), ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <input
                type="text"
                class="form-control"
                id="full_name"
                name="full_name"
                value="<?= htmlspecialchars((string)($address['full_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <div class="col-md-6">
            <label for="phone" class="form-label">
                <?= htmlspecialchars(__('page.addresses.new.form.phone.label', 'Телефон'), ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <input
                type="text"
                class="form-control"
                id="phone"
                name="phone"
                value="<?= htmlspecialchars((string)($address['phone'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <div class="col-md-4">
            <label for="country" class="form-label">
                <?= htmlspecialchars(__('page.addresses.new.form.country.label', 'Країна'), ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <input
                type="text"
                class="form-control"
                id="country"
                name="country"
                value="<?= htmlspecialchars((string)($address['country'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <div class="col-md-4">
            <label for="region" class="form-label">
                <?= htmlspecialchars(__('page.addresses.new.form.region.label', 'Область'), ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <input
                type="text"
                class="form-control"
                id="region"
                name="region"
                value="<?= htmlspecialchars((string)($address['region'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <div class="col-md-4">
            <label for="city" class="form-label">
                <?= htmlspecialchars(__('page.addresses.new.form.city.label', 'Місто'), ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <input
                type="text"
                class="form-control"
                id="city"
                name="city"
                value="<?= htmlspecialchars((string)($address['city'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <div class="col-md-4">
            <label for="postal_code" class="form-label">
                <?= htmlspecialchars(__('page.addresses.new.form.postal_code.label', 'Поштовий індекс'), ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <input
                type="text"
                class="form-control"
                id="postal_code"
                name="postal_code"
                value="<?= htmlspecialchars((string)($address['postal_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <div class="col-md-8">
            <label for="street_address" class="form-label">
                <?= htmlspecialchars(__('page.addresses.new.form.street.label', 'Вулиця, будинок, квартира'), ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <input
                type="text"
                class="form-control"
                id="street_address"
                name="street_address"
                value="<?= htmlspecialchars((string)($address['street_address'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <div class="col-12">
            <label for="comment" class="form-label">
                <?= htmlspecialchars(__('page.addresses.edit.form.comment.label', 'Коментар (необов’язково)'), ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <textarea
                class="form-control"
                id="comment"
                name="comment"
                rows="2"
            ><?= htmlspecialchars((string)($address['comment'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <?= htmlspecialchars(__('ui.save_changes', 'Зберегти зміни'), ENT_QUOTES, 'UTF-8'); ?>
            </button>
            <a href="/profile/addresses" class="btn btn-secondary ms-2">
                <?= htmlspecialchars(__('ui.cancel', 'Скасувати'), ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </div>
    </form>
</div>
