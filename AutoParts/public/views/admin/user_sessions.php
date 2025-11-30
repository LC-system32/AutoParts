<?php
/**
 * @var string|null $flash
 * @var int $userId
 * @var array<int,array<string,mixed>>|mixed $sessions
 */

$flash    = $flash ?? null;
$userId   = isset($userId) ? (int)$userId : 0;
$sessions = $sessions ?? [];

// 🔧 Нормалізація того, що прийшло у $sessions (рядок/об'єкт/обгортка data)
if (is_string($sessions)) {
    $decoded = json_decode($sessions, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $sessions = $decoded;
    }
}
if (is_object($sessions)) {
    $sessions = json_decode(json_encode($sessions), true);
}
if (isset($sessions['data'])) {
    $sessions = $sessions['data'];
}
if (!is_array($sessions)) {
    $sessions = [];
}

$section = 'users';

// Тексти для JS confirm (одразу тут, щоб не дублювати у середині розмітки)
$confirmText = __('admin.users.sessions.terminate_confirm', 'Завершити всі сесії цього користувача?');
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
                                <div class="rounded-circle bg-info d-inline-flex align-items-center justify-content-center me-3"
                                     style="width:48px;height:48px;">
                                    <i class="bi bi-clock-history fs-4"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.users.sessions.title', 'Сесії користувача'); ?>
                                        #<?= (int)$userId; ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.users.sessions.subtitle', 'Перегляд активних та історичних токенів авторизації.'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row gap-2">
                                <a href="/admin/users/<?= (int)$userId; ?>/edit"
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left-short me-1"></i>
                                    <?= __('admin.users.sessions.back_to_user', 'До користувача'); ?>
                                </a>
                                <form method="post"
                                      action="/admin/users/<?= (int)$userId; ?>/sessions/terminate"
                                      onsubmit="return confirm(<?= json_encode($confirmText, JSON_UNESCAPED_UNICODE); ?>);">
                                    <?= \App\Core\Csrf::csrfInput(); ?>
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-x-circle me-1"></i>
                                        <?= __('admin.users.sessions.terminate_button', 'Завершити всі сесії'); ?>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <?php if (!empty($flash)): ?>
                            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light small text-muted">
                                <tr>
                                    <th><?= __('admin.users.sessions.th_id', 'ID'); ?></th>
                                    <th><?= __('admin.users.sessions.th_ip', 'IP'); ?></th>
                                    <th><?= __('admin.users.sessions.th_user_agent', 'User-Agent'); ?></th>
                                    <th><?= __('admin.users.sessions.th_created_at', 'Створено'); ?></th>
                                    <th><?= __('admin.users.sessions.th_expires_at', 'Дійсна до'); ?></th>
                                </tr>
                                </thead>
                                <tbody class="small">
                                <?php if (!empty($sessions)): ?>
                                    <?php foreach ($sessions as $s): ?>
                                        <tr>
                                            <td>#<?= htmlspecialchars((string)($s['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars((string)($s['ip_address'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="text-break" style="max-width: 460px;">
                                                <?= htmlspecialchars((string)($s['user_agent'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td><?= htmlspecialchars((string)($s['created_at'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars((string)($s['expires_at'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <?= __('admin.users.sessions.empty', 'Сесій не знайдено.'); ?>
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
