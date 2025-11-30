<?php
/**
 * View: Admin → Edit user
 *
 * Очікує (гнучко, з авто-нормалізацією):
 * @var string|null $flash
 * @var array|object|null $user         // може бути масив, stdClass або {data:{...}}
 * @var array|object|null $roles        // може бути масив, stdClass або {data:[...]}
 * @var array<int,int>    $userRoleIds
 * @var int|null          $userId       // (бажано передавати з контролера)
 */

$flash       = $flash       ?? null;
$user        = $user        ?? null;
$roles       = $roles       ?? [];
$userRoleIds = $userRoleIds ?? [];

/** Нормалізація users/roles до асоціативних масивів */
$toArray = static function ($v) {
    if (is_array($v)) return $v;
    if (is_object($v)) return json_decode(json_encode($v), true);
    return [];
};

$user  = $toArray($user);
$roles = $toArray($roles);

/** Розпакувати {data: ...} якщо так прийшло */
if (isset($user['data']))  $user  = $toArray($user['data']);
if (isset($roles['data'])) $roles = $toArray($roles['data']);

/** ID користувача для заголовку/посилань: пріоритет route-ід, далі з $user */
$viewUserId = isset($userId) ? (int)$userId : (int)($user['id'] ?? 0);

/** Якщо масив ролей id не передали – витягнути з user.role_ids або user.roles */
if (empty($userRoleIds) && isset($user['role_ids']) && is_array($user['role_ids'])) {
    $userRoleIds = array_map('intval', $user['role_ids']);
} elseif (empty($userRoleIds) && isset($user['roles']) && is_array($user['roles'])) {
    foreach ($user['roles'] as $r) {
        if (is_array($r) && isset($r['id'])) $userRoleIds[] = (int)$r['id'];
    }
}

/** Перевірка ролі (для чекбоксів) */
function isCheckedRole_edit(array $userRoleIds, $roleId): bool {
    return in_array((int)$roleId, array_map('intval', $userRoleIds), true);
}

$section = 'users';
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
                                <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                                    <i class="bi bi-person-gear fs-4"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?php if ($viewUserId > 0): ?>
                                            <?= __('admin.users.edit.title_edit'); ?>
                                            #<?= htmlspecialchars((string)$viewUserId, ENT_QUOTES, 'UTF-8'); ?>
                                        <?php else: ?>
                                            <?= __('admin.users.edit.title_create'); ?>
                                        <?php endif; ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.users.edit.subtitle'); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="/admin/users" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left-short me-1"></i>
                                    <?= __('admin.users.edit.back'); ?>
                                </a>
                            </div>
                        </div>

                        <?php if (!empty($flash)): ?>
                            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                                <button type="button"
                                        class="btn-close"
                                        data-bs-dismiss="alert"
                                        aria-label="<?= __('common.close'); ?>"></button>
                            </div>
                        <?php endif; ?>

                        <?php
                        $action = $viewUserId > 0
                            ? '/admin/users/' . $viewUserId . '/update'
                            : '/admin/users/create';
                        ?>

                        <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>" class="row g-3">
                            <?= \App\Core\Csrf::csrfInput(); ?>

                            <?php if ($viewUserId > 0): ?>
                                <input type="hidden" name="user_id" value="<?= (int)$viewUserId; ?>">
                            <?php endif; ?>

                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.email'); ?>
                                </label>
                                <input type="email" name="email" required class="form-control form-control-sm"
                                       value="<?= htmlspecialchars((string)($user['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.login'); ?>
                                </label>
                                <input type="text" name="login" required class="form-control form-control-sm"
                                       value="<?= htmlspecialchars((string)($user['login'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.password'); ?>
                                    <span class="text-muted">
                                        (<?= __('admin.users.edit.password_hint'); ?>)
                                    </span>
                                </label>
                                <input type="password" name="password" class="form-control form-control-sm" autocomplete="new-password">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.password_confirm'); ?>
                                </label>
                                <input type="password" name="password_confirm" class="form-control form-control-sm" autocomplete="new-password">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.first_name'); ?>
                                </label>
                                <input type="text" name="first_name" class="form-control form-control-sm"
                                       value="<?= htmlspecialchars((string)($user['first_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.last_name'); ?>
                                </label>
                                <input type="text" name="last_name" class="form-control form-control-sm"
                                       value="<?= htmlspecialchars((string)($user['last_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.phone'); ?>
                                </label>
                                <input type="text" name="phone" class="form-control form-control-sm"
                                       value="<?= htmlspecialchars((string)($user['phone'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.status'); ?>
                                </label>
                                <div class="form-check form-switch">
                                    <?php $isActive = array_key_exists('is_active', $user) ? (bool)$user['is_active'] : true; ?>
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= $isActive ? 'checked' : ''; ?>>
                                    <label class="form-check-label small" for="is_active">
                                        <?= __('admin.users.edit.status_label'); ?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="form-label small text-muted mb-1">
                                    <?= __('admin.users.edit.roles'); ?>
                                </label>
                                <div class="border rounded-3 p-2 bg-light">
                                    <?php if (!empty($roles)): ?>
                                        <div class="row g-1">
                                            <?php foreach ($roles as $role): ?>
                                                <?php
                                                $rid  = (int)($role['id'] ?? 0);
                                                $code = (string)($role['code'] ?? '');
                                                $name = (string)($role['name'] ?? '');
                                                ?>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="roles[]"
                                                               value="<?= $rid; ?>" id="role_<?= $rid; ?>"
                                                            <?= isCheckedRole_edit($userRoleIds, $rid) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label small" for="role_<?= $rid; ?>">
                                                            <span class="fw-semibold"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></span>
                                                            <?php if ($name !== ''): ?> – <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?><?php endif; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="small text-muted mb-0">
                                            <?= __('admin.users.edit.roles_empty'); ?>
                                            <code>roles</code>.
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-12 border-top pt-3 mt-2 d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-check2-circle me-1"></i>
                                    <?= __('admin.users.edit.save_button'); ?>
                                </button>
                                <?php if ($viewUserId > 0): ?>
                                    <a href="/admin/users/<?= (int)$viewUserId; ?>/sessions" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-clock-history me-1"></i>
                                        <?= __('admin.users.edit.sessions_button'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>

                    </div>
                </div>
            </div><!-- /main -->
        </div>
    </div>
</section>
