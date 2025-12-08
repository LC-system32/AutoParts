<?php
/**
 * View: Admin → Users list
 *
 * Очікує:
 * @var string|null $flash
 * @var array<int,array<string,mixed>>|array{data:array} $users
 * @var array<int,array<string,mixed>>|array{data:array} $roles
 * @var array<string,mixed> $filters
 */

$flash   = $flash   ?? null;
$users   = $users   ?? [];
$roles   = $roles   ?? [];
$filters = $filters ?? ['q'=>'','role'=>'','status'=>''];

$section = 'users';

/** Нормалізація на випадок відповіді формату { success, data } */
if (is_array($users) && isset($users['data']) && is_array($users['data'])) {
    $users = $users['data'];
}
if (is_array($roles) && isset($roles['data']) && is_array($roles['data'])) {
    $roles = $roles['data'];
}

/**
 * Нормалізує поле roles користувача до масиву рядків для відображення.
 * Підтримує: масив об’єктів ролей, масив рядків, JSON-рядок, "admin,manager".
 */
function extractRoleLabels($rawRoles): array {
    $rolesArr = $rawRoles ?? [];

    if (is_string($rolesArr)) {
        $decoded = json_decode($rolesArr, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $rolesArr = $decoded;
        } else {
            $rolesArr = array_values(array_filter(array_map('trim', explode(',', $rolesArr))));
        }
    }
    if (is_array($rolesArr) && (isset($rolesArr['code']) || isset($rolesArr['name']))) {
        $rolesArr = [$rolesArr];
    }

    $labels = [];
    foreach ((array)$rolesArr as $r) {
        $label = is_array($r) ? (string)($r['name'] ?? $r['code'] ?? '') : (string)$r;
        $label = trim($label);
        if ($label !== '') $labels[] = $label;
    }
    $labels = array_values(array_unique($labels));
    sort($labels, SORT_NATURAL | SORT_FLAG_CASE);
    return $labels;
}
?>
<section class="py-3 py-md-4">
    <div class="container-fluid">
        <div class="row g-3">
            <?php include '_sidebar.php'; ?>

            <div class="col-12 col-lg-9 col-xl-10">
                <div class="card shadow-sm border-0 rounded-4 mb-3">
                    <div class="card-body p-3 p-md-4">
                        <!-- Заголовок + кнопка -->
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                                    <i class="bi bi-people fs-4 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.users.title'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.users.subtitle'); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="/admin/users/create" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    <?= __('admin.users.create_button'); ?>
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

                        <!-- Фільтри -->
                        <form method="get" action="/admin/users" class="border rounded-4 p-3 bg-light mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label class="form-label small text-muted mb-1">
                                        <?= __('admin.users.filters.search.label'); ?>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text"
                                               name="q"
                                               class="form-control border-start-0"
                                               placeholder="<?= __('admin.users.filters.search.placeholder'); ?>"
                                               value="<?= htmlspecialchars((string)($filters['q'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>

                                <div class="col-6 col-md-3">
                                    <label class="form-label small text-muted mb-1">
                                        <?= __('admin.users.filters.role.label'); ?>
                                    </label>
                                    <select name="role" class="form-select form-select-sm">
                                        <option value="">
                                            <?= __('admin.users.filters.role.all'); ?>
                                        </option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= htmlspecialchars((string)($role['code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                                <?= (($filters['role'] ?? '') === ($role['code'] ?? '')) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars((string)($role['name'] ?? $role['code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-6 col-md-3">
                                    <label class="form-label small text-muted mb-1">
                                        <?= __('admin.users.filters.status.label'); ?>
                                    </label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">
                                            <?= __('admin.users.filters.status.all'); ?>
                                        </option>
                                        <option value="active"   <?= (($filters['status'] ?? '') === 'active')   ? 'selected' : ''; ?>>
                                            <?= __('admin.users.filters.status.active'); ?>
                                        </option>
                                        <option value="blocked"  <?= (($filters['status'] ?? '') === 'blocked')  ? 'selected' : ''; ?>>
                                            <?= __('admin.users.filters.status.blocked'); ?>
                                        </option>
                                        <option value="inactive" <?= (($filters['status'] ?? '') === 'inactive') ? 'selected' : ''; ?>>
                                            <?= __('admin.users.filters.status.inactive'); ?>
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-2 d-grid d-md-flex gap-2 justify-content-md-end">
                                    <button type="submit" class="btn btn-dark btn-sm">
                                        <i class="bi bi-funnel me-1"></i>
                                        <?= __('admin.users.filters.apply'); ?>
                                    </button>
                                    <a href="/admin/users" class="btn btn-outline-secondary btn-sm">
                                        <?= __('admin.users.filters.reset'); ?>
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Таблиця користувачів -->
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light small text-muted">
                                <tr>
                                    <th class="text-nowrap">
                                        <?= __('admin.users.table.header.id'); ?>
                                    </th>
                                    <th class="text-nowrap">
                                        <?= __('admin.users.table.header.login_email'); ?>
                                    </th>
                                    <th class="text-nowrap">
                                        <?= __('admin.users.table.header.name'); ?>
                                    </th>
                                    <th class="text-nowrap">
                                        <?= __('admin.users.table.header.phone'); ?>
                                    </th>
                                    <th class="text-nowrap">
                                        <?= __('admin.users.table.header.roles'); ?>
                                    </th>
                                    <th class="text-nowrap">
                                        <?= __('admin.users.table.header.status'); ?>
                                    </th>
                                    <th class="text-nowrap">
                                        <?= __('admin.users.table.header.created_at'); ?>
                                    </th>
                                    <th class="text-end text-nowrap">
                                        <?= __('admin.users.table.header.actions'); ?>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="small">
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <?php
                                        $id         = (int)($user['id'] ?? 0);
                                        $login      = (string)($user['login'] ?? '');
                                        $email      = (string)($user['email'] ?? '');
                                        $fullName   = trim((string)($user['first_name'] ?? '') . ' ' . (string)($user['last_name'] ?? ''));
                                        $phone      = (string)($user['phone'] ?? '');
                                        $isActive   = (bool)($user['is_active'] ?? true);
                                        $createdAt  = (string)($user['created_at'] ?? '');
                                        $roleLabels = extractRoleLabels($user['roles'] ?? []);
                                        ?>
                                        <tr class="<?= $isActive ? '' : 'table-secondary'; ?>">
                                            <td class="text-muted">
                                                #<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>
                                            </td>

                                            <td>
                                                <div class="fw-semibold">
                                                    <?= htmlspecialchars($login, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                                <div class="text-muted">
                                                    <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($fullName !== '' ? $fullName : '—', ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($phone !== '' ? $phone : '—', ENT_QUOTES, 'UTF-8'); ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($roleLabels)): ?>
                                                    <?php foreach ($roleLabels as $label): ?>
                                                        <span class="badge bg-light text-dark rounded-pill me-1 mb-1">
                                                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if ($isActive): ?>
                                                    <span class="badge bg-success rounded-pill">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        <?= __('admin.users.status.active'); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary rounded-pill">
                                                        <i class="bi bi-slash-circle me-1"></i>
                                                        <?= __('admin.users.status.blocked'); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-nowrap">
                                                <?= htmlspecialchars($createdAt !== '' ? $createdAt : '—', ENT_QUOTES, 'UTF-8'); ?>
                                            </td>

                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="/admin/users/<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>/edit"
                                                       class="btn btn-outline-secondary btn-sm"
                                                       title="<?= __('admin.users.actions.edit'); ?>">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="/admin/users/<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>/sessions"
                                                       class="btn btn-outline-secondary btn-sm"
                                                       title="<?= __('admin.users.actions.sessions'); ?>">
                                                        <i class="bi bi-clock-history"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="bi bi-emoji-frown fs-3 d-block mb-2"></i>
                                            <div class="fw-semibold mb-1">
                                                <?= __('admin.users.empty.title'); ?>
                                            </div>
                                            <div class="small">
                                                <?= __('admin.users.empty.text'); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div><!-- /main -->
        </div>
    </div>
</section>
