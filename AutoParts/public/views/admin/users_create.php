<?php
/**
 * @var string|null $flash
 * @var array<int,array<string,mixed>> $roles
 * @var array<string,mixed> $old
 * @var array<string,string> $errors
 */

$flash   = $flash   ?? null;
$roles   = $roles   ?? [];
$old     = $old     ?? [];     // значення полів після невдалої спроби
$errors  = $errors  ?? [];     // помилки валідації (per-field)
$section = 'users';

$oldRoleIds = array_map('intval', $old['roles'] ?? []);

function oldval(array $old, string $key, string $default = ''): string {
    return htmlspecialchars((string)($old[$key] ?? $default), ENT_QUOTES, 'UTF-8');
}
function isInvalid(array $errors, string $key): string {
    return empty($errors[$key]) ? '' : ' is-invalid';
}
function feedback(array $errors, string $key): void {
    if (!empty($errors[$key])) {
        echo '<div class="invalid-feedback">'.htmlspecialchars($errors[$key], ENT_QUOTES, 'UTF-8').'</div>';
    }
}
function isCheckedRole_create(array $oldRoleIds, $roleId): bool {
    return in_array((int)$roleId, $oldRoleIds, true);
}
$isActiveChecked = array_key_exists('is_active', $old) ? (bool)$old['is_active'] : true;
?>

<section class="py-3 py-md-4">
    <div class="container-fluid">
        <div class="row">
            <?php include '_sidebar.php'; ?>

            <div class="col-12 col-lg-9 col-xl-10">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center me-3"
                                     style="width:48px;height:48px;">
                                    <i class="bi bi-person-plus fs-4 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.users.edit.title_create', 'Створення користувача'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.users.edit.subtitle_create', 'Заповніть дані нового користувача та призначте ролі доступу.'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row gap-2">
                                <a href="/admin/users" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left-short me-1"></i>
                                    <?= __('admin.users.edit.back', 'До списку'); ?>
                                </a>
                            </div>
                        </div>

                        <?php if (!empty($flash)): ?>
                            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php $action = '/admin/users/create'; ?>

                        <form method="post"
                              action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>"
                              class="row g-3" novalidate>
                            <?= \App\Core\Csrf::csrfInput(); ?>

                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.email', 'Email'); ?>
                                </label>
                                <input type="email"
                                       name="email"
                                       required
                                       class="form-control form-control-sm<?= isInvalid($errors, 'email'); ?>"
                                       value="<?= oldval($old, 'email'); ?>">
                                <?php feedback($errors, 'email'); ?>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.login', 'Логін'); ?>
                                </label>
                                <input type="text"
                                       name="login"
                                       required
                                       class="form-control form-control-sm<?= isInvalid($errors, 'login'); ?>"
                                       value="<?= oldval($old, 'login'); ?>">
                                <?php feedback($errors, 'login'); ?>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.password', 'Пароль'); ?>
                                </label>
                                <input type="password"
                                       name="password"
                                       required
                                       minlength="8"
                                       class="form-control form-control-sm<?= isInvalid($errors, 'password'); ?>"
                                       autocomplete="new-password">
                                <?php feedback($errors, 'password'); ?>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.password_confirm', 'Підтвердження пароля'); ?>
                                </label>
                                <input type="password"
                                       name="password_confirm"
                                       required
                                       minlength="8"
                                       class="form-control form-control-sm<?= isInvalid($errors, 'password_confirm'); ?>"
                                       autocomplete="new-password">
                                <?php feedback($errors, 'password_confirm'); ?>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.first_name', 'Ім’я'); ?>
                                </label>
                                <input type="text"
                                       name="first_name"
                                       class="form-control form-control-sm<?= isInvalid($errors, 'first_name'); ?>"
                                       value="<?= oldval($old, 'first_name'); ?>">
                                <?php feedback($errors, 'first_name'); ?>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.last_name', 'Прізвище'); ?>
                                </label>
                                <input type="text"
                                       name="last_name"
                                       class="form-control form-control-sm<?= isInvalid($errors, 'last_name'); ?>"
                                       value="<?= oldval($old, 'last_name'); ?>">
                                <?php feedback($errors, 'last_name'); ?>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.phone', 'Телефон'); ?>
                                </label>
                                <input type="text"
                                       name="phone"
                                       class="form-control form-control-sm<?= isInvalid($errors, 'phone'); ?>"
                                       value="<?= oldval($old, 'phone'); ?>">
                                <?php feedback($errors, 'phone'); ?>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.status', 'Статус'); ?>
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="is_active"
                                           name="is_active"
                                           <?= $isActiveChecked ? 'checked' : ''; ?>>
                                    <label class="form-check-label small" for="is_active">
                                        <?= __('admin.users.edit.status_label', 'Активний обліковий запис'); ?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.roles', 'Ролі доступу'); ?>
                                </label>
                                <div class="border rounded-3 p-2 bg-light">
                                    <?php if (!empty($roles)): ?>
                                        <div class="row g-1">
                                            <?php foreach ($roles as $role): ?>
                                                <?php $rid = (int)($role['id'] ?? 0); ?>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-check form-check-sm">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               name="roles[]"
                                                               value="<?= $rid; ?>"
                                                               id="role_<?= $rid; ?>"
                                                               <?= isCheckedRole_create($oldRoleIds, $rid) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label small" for="role_<?= $rid; ?>">
                                                            <span class="fw-semibold">
                                                                <?= htmlspecialchars((string)($role['code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                            <?php if (!empty($role['name'])): ?>
                                                                – <?= htmlspecialchars((string)$role['name'], ENT_QUOTES, 'UTF-8'); ?>
                                                            <?php endif; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="small text-muted mb-0">
                                            <?= __('admin.users.edit.roles_empty', 'Ролі ще не налаштовані. Спочатку створіть їх у таблиці roles.'); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <?php feedback($errors, 'roles'); ?>
                            </div>

                            <div class="col-12 border-top pt-3 mt-2 d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-check2-circle me-1"></i>
                                    <?= __('admin.users.edit.create_button', 'Створити користувача'); ?>
                                </button>
                                <span></span>
                            </div>
                        </form>

                        <script>
                        // Невеликий автотрім + клієнтська перевірка пароля (тексти теж багатомовні)
                        (function(){
                            const form = document.currentScript.closest('form');
                            if (!form) return;

                            const msgPassLength = <?= json_encode(__('admin.users.edit.client_error_password_length', 'Пароль має містити щонайменше 8 символів.')); ?>;
                            const msgPassMismatch = <?= json_encode(__('admin.users.edit.client_error_password_mismatch', 'Паролі не співпадають.')); ?>;

                            form.addEventListener('submit', function(e){
                                ['email','login','first_name','last_name','phone'].forEach(function(name){
                                    const el = form.querySelector('[name="'+name+'"]');
                                    if (el && typeof el.value === 'string') el.value = el.value.trim();
                                });
                                const pass = form.querySelector('[name="password"]');
                                const conf = form.querySelector('[name="password_confirm"]');
                                if (pass && pass.value && pass.value.length < 8) {
                                    e.preventDefault();
                                    pass.classList.add('is-invalid');
                                    alert(msgPassLength);
                                } else if (pass && conf && pass.value !== conf.value) {
                                    e.preventDefault();
                                    conf.classList.add('is-invalid');
                                    alert(msgPassMismatch);
                                }
                            });
                        })();
                        </script>

                    </div>
                </div>
            </div><!-- /main -->
        </div>
    </div>
</section>
