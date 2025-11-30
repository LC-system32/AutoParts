<?php
/** @var array<int,array<string,mixed>> $tickets */
/** @var array<string,mixed>|null       $activeTicket */
/** @var string|null                    $flash */

$tickets      = $tickets ?? [];
$activeTicket = $activeTicket ?? null;
$flash        = $flash ?? null;

$badgeClass = static function (?string $status): string {
    $status = (string)$status;
    return match ($status) {
        'open'        => 'bg-warning text-dark',
        'in_progress' => 'bg-info text-dark',
        'closed'      => 'bg-success',
        default       => 'bg-secondary',
    };
};

$supportStatusLabel = static function (?string $status): string {
    $status = (string)$status;
    return match ($status) {
        'open'        => __('support.status.open'),
        'in_progress' => __('support.status.in_progress'),
        'closed'      => __('support.status.closed'),
        default       => $status,
    };
};
?>
<section class="mb-4">
    <!-- HERO -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body px-3 px-md-4 py-3 py-md-4 d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div class="d-flex align-items-start gap-2">
                <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-warning-subtle" style="width:48px;height:48px;">
                    <i class="bi bi-headset text-warning fs-4"></i>
                </span>
                <div>
                    <h1 class="h4 h3-md mb-1 fw-semibold">
                        <?= htmlspecialchars(__('page.support.title'), ENT_QUOTES, 'UTF-8'); ?>
                    </h1>
                    <p class="text-muted mb-0">
                        <?= __('page.support.hero.subtitle'); ?>
                    </p>
                </div>
            </div>
            <?php if (!empty($_SESSION['user'])): ?>
                <div class="text-md-end">
                    <div class="d-inline-flex flex-column align-items-md-end gap-1">
                        <span class="badge rounded-pill text-bg-warning text-dark fw-semibold px-3 py-2">
                            <i class="bi bi-inbox me-1"></i>
                            <?= __('page.support.hero.requests_label'); ?> <?= count($tickets); ?>
                        </span>
                        <span class="small text-muted">
                            <?= __('page.support.hero.response_time'); ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- FLASH -->
    <?php if (!empty($flash)): ?>
        <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
            <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="<?= htmlspecialchars(__('ui.close', 'Закрити'), ENT_QUOTES, 'UTF-8'); ?>"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['user'])): ?>
        <!-- GUEST -->
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width:72px;height:72px;">
                            <i class="bi bi-person-exclamation fs-1 text-muted"></i>
                        </div>
                    </div>
                    <h2 class="h4 fw-semibold mb-2">
                        <?= __('page.support.guest.title'); ?>
                    </h2>
                    <p class="text-muted mb-4">
                        <?= __('page.support.guest.text'); ?>
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                        <a href="/login" class="btn btn-warning text-dark fw-semibold px-4">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            <?= __('page.support.guest.login'); ?>
                        </a>
                        <a href="/register" class="btn btn-outline-secondary fw-semibold px-4">
                            <i class="bi bi-person-plus me-1"></i>
                            <?= __('page.support.guest.register'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>

        <div class="row g-3 g-md-4">
            <!-- LEFT: CREATE + LIST -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-header bg-white border-0 py-3">
                        <h2 class="h6 fw-semibold mb-0">
                            <i class="bi bi-envelope-plus me-2 text-warning"></i>
                            <?= __('page.support.form.title'); ?>
                        </h2>
                    </div>
                    <div class="card-body">
                        <form action="/support" method="post" novalidate>
                            <?= \App\Core\Csrf::csrfInput(); ?>
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-semibold text-uppercase">
                                    <?= __('page.support.form.subject.label'); ?>
                                </label>
                                <input type="text"
                                       name="subject"
                                       class="form-control"
                                       placeholder="<?= htmlspecialchars(__('page.support.form.subject.placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                                       minlength="5"
                                       maxlength="200"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-semibold text-uppercase">
                                    <?= __('page.support.form.message.label'); ?>
                                </label>
                                <textarea name="message"
                                          rows="5"
                                          class="form-control"
                                          placeholder="<?= htmlspecialchars(__('page.support.form.message.placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                                          minlength="10"
                                          maxlength="4000"
                                          required></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning text-dark fw-semibold">
                                <i class="bi bi-send me-1"></i>
                                <?= __('page.support.form.submit'); ?>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h2 class="h6 fw-semibold mb-0">
                            <i class="bi bi-inbox me-2 text-warning"></i>
                            <?= __('page.support.list.title'); ?>
                        </h2>
                        <span class="small text-muted">
                            <?= __('page.support.list.hint'); ?>
                        </span>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (empty($tickets)): ?>
                            <div class="list-group-item text-center text-muted">
                                <?= __('page.support.list.empty'); ?>
                            </div>
                        <?php else: ?>
                            <?php foreach ($tickets as $t): ?>
                                <?php
                                $id        = (int)($t['id'] ?? 0);
                                $subject   = (string)($t['subject'] ?? '');
                                $status    = (string)($t['status'] ?? '');
                                $createdAt = !empty($t['created_at'])
                                    ? date('d.m.Y H:i', strtotime((string)$t['created_at']))
                                    : '';
                                ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div class="pe-2">
                                            <div class="fw-semibold mb-1">
                                                <?= htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                            <?php if ($createdAt): ?>
                                                <div class="small text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    <?= htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge <?= $badgeClass($status); ?>">
                                                <?= htmlspecialchars($supportStatusLabel($status), ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                            <a href="/support?ticket=<?= $id; ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-chat-dots me-1"></i>
                                                <?= __('page.support.list.open'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT: ACTIVE DIALOG -->
            <div class="col-12 col-lg-6">
                <?php if (!$activeTicket): ?>
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body d-flex align-items-center justify-content-center text-center text-muted">
                            <div>
                                <i class="bi bi-chat-left-text fs-1 d-block mb-2"></i>
                                <?= __('page.support.detail.empty'); ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php
                    $tid     = (int)($activeTicket['id'] ?? 0);
                    $subject = (string)($activeTicket['subject'] ?? '');
                    $status  = (string)($activeTicket['status']  ?? '');
                    $created = !empty($activeTicket['created_at'])
                        ? date('d.m.Y H:i', strtotime((string)$activeTicket['created_at']))
                        : '';
                    $msgs    = is_array($activeTicket['messages'] ?? null) ? $activeTicket['messages'] : [];
                    ?>
                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                    <div class="small text-muted">
                                        <?php if ($created): ?>
                                            <i class="bi bi-clock me-1"></i>
                                            <?= htmlspecialchars($created, ENT_QUOTES, 'UTF-8'); ?> ·
                                        <?php endif; ?>
                                        <span class="badge <?= $badgeClass($status); ?> align-baseline">
                                            <?= htmlspecialchars($supportStatusLabel($status), ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </div>
                                </div>
                                <a class="btn btn-sm btn-outline-secondary" href="/support">
                                    <i class="bi bi-x-circle me-1"></i>
                                    <?= __('page.support.detail.close'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($msgs)): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-chat-left-text fs-1 d-block mb-2"></i>
                                    <?= __('page.support.detail.no_messages'); ?>
                                </div>
                            <?php else: ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($msgs as $m): ?>
                                        <?php
                                        $body    = (string)($m['body'] ?? '');
                                        $when    = !empty($m['created_at'])
                                            ? date('d.m.Y H:i', strtotime((string)$m['created_at']))
                                            : '';
                                        $isStaff = (bool)($m['is_staff'] ?? false);
                                        $author  = $isStaff
                                            ? __('page.support.detail.author.staff')
                                            : __('page.support.detail.author.user');
                                        ?>
                                        <div class="d-flex <?= $isStaff ? 'justify-content-end' : 'justify-content-start'; ?>">
                                            <div class="p-3 rounded-4 shadow-sm <?= $isStaff ? 'bg-warning-subtle' : 'bg-light'; ?>"
                                                 style="max-width: 85%;">
                                                <div class="small text-muted mb-1">
                                                    <?= $isStaff
                                                        ? '<i class="bi bi-shield-check me-1"></i>'
                                                        : '<i class="bi bi-person-circle me-1"></i>'; ?>
                                                    <?= htmlspecialchars($author, ENT_QUOTES, 'UTF-8'); ?>
                                                    <?php if ($when): ?>
                                                        · <?= htmlspecialchars($when, ENT_QUOTES, 'UTF-8'); ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div><?= nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8')); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($status !== 'closed'): ?>
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-0 py-3">
                                <h3 class="h6 fw-semibold mb-0">
                                    <i class="bi bi-reply-fill me-2 text-warning"></i>
                                    <?= __('page.support.reply.title'); ?>
                                </h3>
                            </div>
                            <div class="card-body">
                                <form action="/support/reply" method="post" novalidate>
                                    <?= \App\Core\Csrf::csrfInput(); ?>
                                    <input type="hidden" name="ticket_id" value="<?= $tid; ?>">
                                    <div class="mb-3">
                                        <textarea name="message"
                                                  rows="4"
                                                  class="form-control"
                                                  placeholder="<?= htmlspecialchars(__('page.support.reply.placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                                                  required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning text-dark fw-semibold">
                                        <i class="bi bi-send me-1"></i>
                                        <?= __('page.support.reply.submit'); ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
