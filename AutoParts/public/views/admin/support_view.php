<?php
/**
 * @var string|null                    $flash
 * @var array<string,mixed>           $ticket
 * @var array<int,array<string,mixed>> $messages
 */

$flash    = $flash    ?? null;
$ticket   = $ticket   ?? [];
$messages = $messages ?? [];

$section  = 'support';

$ticketId = (int)($ticket['id'] ?? 0);
$status   = (string)($ticket['status'] ?? 'open');
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
                                    <i class="bi bi-life-preserver fs-4 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.support.show.heading'); ?> #<?= $ticketId; ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.support.show.subtitle'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row gap-2">
                                <a href="/admin/support" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left-short me-1"></i>
                                    <?= __('admin.support.show.back_button'); ?>
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

                        <div class="row g-3">
                            <!-- Дані тікета -->
                            <div class="col-12 col-lg-4">
                                <div class="border rounded-4 p-3 bg-white h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-muted text-uppercase fw-semibold">
                                            <?= __('admin.support.show.info.title'); ?>
                                        </span>
                                        <?php
                                        $class = 'bg-secondary-subtle text-secondary';
                                        if ($status === 'open') {
                                            $class = 'bg-danger-subtle text-danger';
                                        } elseif ($status === 'in_progress') {
                                            $class = 'bg-warning-subtle text-warning';
                                        } elseif ($status === 'closed') {
                                            $class = 'bg-success-subtle text-success';
                                        }

                                        $statusKey   = 'admin.support.show.status.' . $status;
                                        $statusLabel = __($statusKey);
                                        if ($statusLabel === $statusKey) {
                                            $statusLabel = $status;
                                        }
                                        ?>
                                        <span class="badge <?= $class; ?>">
                                            <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </div>

                                    <dl class="row small mb-0">
                                        <dt class="col-4 text-muted">
                                            <?= __('admin.support.show.info.subject'); ?>
                                        </dt>
                                        <dd class="col-8">
                                            <?= htmlspecialchars((string)($ticket['subject'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                        </dd>

                                        <dt class="col-4 text-muted">
                                            <?= __('admin.support.show.info.customer'); ?>
                                        </dt>
                                        <dd class="col-8">
                                            <?= htmlspecialchars((string)($ticket['customer_name'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                            <br>
                                            <span class="text-muted">
                                                <?= htmlspecialchars((string)($ticket['customer_email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </dd>

                                        <dt class="col-4 text-muted">
                                            <?= __('admin.support.show.info.date'); ?>
                                        </dt>
                                        <dd class="col-8">
                                            <?= htmlspecialchars((string)($ticket['created_at'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                        </dd>
                                    </dl>

                                    <!-- Форма зміни статусу -->
                                    <hr class="my-3">
                                    <form method="post"
                                          action="/admin/support/<?= $ticketId; ?>/status"
                                          class="small">
                                        <?= \App\Core\Csrf::csrfInput(); ?>
                                        <label class="form-label small text-muted mb-1">
                                            <?= __('admin.support.show.info.status_label'); ?>
                                        </label>
                                        <select name="status" class="form-select form-select-sm mb-2">
                                            <option value="open" <?= $status === 'open' ? 'selected' : ''; ?>>
                                                <?= __('admin.support.show.status.open'); ?>
                                            </option>
                                            <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : ''; ?>>
                                                <?= __('admin.support.show.status.in_progress'); ?>
                                            </option>
                                            <option value="closed" <?= $status === 'closed' ? 'selected' : ''; ?>>
                                                <?= __('admin.support.show.status.closed'); ?>
                                            </option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="bi bi-check2-circle me-1"></i>
                                            <?= __('admin.support.show.status.update_button'); ?>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Повідомлення -->
                            <div class="col-12 col-lg-8">
                                <div class="border rounded-4 p-3 bg-white h-100 d-flex flex-column">
                                    <div class="small text-muted text-uppercase fw-semibold mb-2">
                                        <?= __('admin.support.show.messages.title'); ?>
                                    </div>

                                    <div class="flex-grow-1 mb-3" style="max-height: 380px; overflow-y: auto;">
                                        <?php if (!empty($messages)): ?>
                                            <?php foreach ($messages as $message): ?>
                                                <?php $isStaff = !empty($message['is_staff']); ?>
                                                <div class="mb-2">
                                                    <div class="d-flex <?= $isStaff ? 'justify-content-end' : 'justify-content-start'; ?>">
                                                        <div class="border rounded-3 px-2 py-1 small
                                                            <?= $isStaff ? 'bg-light-subtle' : 'bg-light'; ?>"
                                                             style="max-width: 80%;">
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="fw-semibold small">
                                                                    <?php
                                                                    $authorName = (string)($message['author_name'] ?? '');
                                                                    if ($authorName === '') {
                                                                        $authorName = $isStaff
                                                                            ? __('admin.support.show.messages.staff_default')
                                                                            : __('admin.support.show.messages.customer_default');
                                                                    }
                                                                    ?>
                                                                    <?= htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8'); ?>
                                                                </span>
                                                                <span class="text-muted small">
                                                                    <?= htmlspecialchars((string)($message['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <?= nl2br(htmlspecialchars((string)($message['body'] ?? ''), ENT_QUOTES, 'UTF-8')); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="small text-muted mb-0">
                                                <?= __('admin.support.show.messages.empty'); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Форма відповіді -->
                                    <form method="post" action="/admin/support/<?= $ticketId; ?>/reply">
                                        <?= \App\Core\Csrf::csrfInput(); ?>
                                        <label class="form-label small text-muted mb-1">
                                            <?= __('admin.support.show.reply.label'); ?>
                                        </label>
                                        <textarea name="body"
                                                  rows="3"
                                                  class="form-control form-control-sm mb-2"
                                                  required></textarea>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="bi bi-send me-1"></i>
                                                <?= __('admin.support.show.reply.submit'); ?>
                                            </button>
                                            <div class="form-check form-check-sm small">
                                                <input class="form-check-input" type="checkbox" id="close_ticket" name="close_ticket">
                                                <label class="form-check-label" for="close_ticket">
                                                    <?= __('admin.support.show.reply.close_after'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div><!-- /row -->
                    </div>
                </div>
            </div><!-- /main -->
        </div>
    </div>
</section>
