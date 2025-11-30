<?php
/**
 * @var string|null                           $flash
 * @var array<int,array<string,mixed>>        $tickets
 * @var array<string,mixed>                   $filters
 */

// --- Початкові значення з контролера (якщо прийшли) ---
$flash   = $flash   ?? null;
$tickets = $tickets ?? [];
$filters = $filters ?? [
    'status' => '',
    'q'      => '',
];

// --- Локальні фільтри прямо на сторінці через $_GET ---
$search = trim((string)($_GET['q'] ?? ($filters['q'] ?? '')));
$status = (string)($_GET['status'] ?? ($filters['status'] ?? ''));

// нормалізація статусу
$allowedStatuses = ['open', 'in_progress', 'closed'];
if (!in_array($status, $allowedStatuses, true)) {
    $status = '';
}

// актуалізуємо $filters, щоб select / input показували обране
$filters['q']      = $search;
$filters['status'] = $status;

// фільтруємо тікети по статусу + пошуку (тема, імʼя, email)
$filteredTickets = array_filter($tickets, function (array $ticket) use ($search, $status) {
    $ticketStatus = (string)($ticket['status'] ?? 'open');

    if ($status !== '' && $ticketStatus !== $status) {
        return false;
    }

    if ($search === '') {
        return true;
    }

    $haystack = mb_strtolower(
        (string)($ticket['subject']        ?? '') . ' ' .
        (string)($ticket['customer_name']  ?? '') . ' ' .
        (string)($ticket['customer_email'] ?? ''),
        'UTF-8'
    );
    $needle = mb_strtolower($search, 'UTF-8');

    return mb_strpos($haystack, $needle) !== false;
});

// сортування: новіші тікети вище (по created_at)
usort($filteredTickets, function (array $a, array $b) {
    $aTime = strtotime((string)($a['created_at'] ?? '')) ?: 0;
    $bTime = strtotime((string)($b['created_at'] ?? '')) ?: 0;
    return $bTime <=> $aTime;
});

$totalTickets = count($filteredTickets);

// для навігації у сайдбарі
$section = 'support';
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
                                        <?= __('admin.support.index.title'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.support.index.subtitle'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row gap-2">
                                <a href="/admin/support?status=open" class="btn btn-danger btn-sm">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    <?= __('admin.support.index.open_tickets_button'); ?>
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
                        <form method="get" action="/admin/support" class="border rounded-4 p-3 bg-light mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label class="form-label small text-muted mb-1">
                                        <?= __('admin.support.index.filters.search_label'); ?>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text"
                                               name="q"
                                               class="form-control border-start-0"
                                               placeholder="<?= __('admin.support.index.filters.search_placeholder'); ?>"
                                               value="<?= htmlspecialchars((string)$filters['q'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>

                                <div class="col-6 col-md-3">
                                    <label class="form-label small text-muted mb-1">
                                        <?= __('admin.support.index.filters.status_label'); ?>
                                    </label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">
                                            <?= __('admin.support.index.filters.status_all'); ?>
                                        </option>
                                        <option value="open" <?= ($filters['status'] ?? '') === 'open' ? 'selected' : ''; ?>>
                                            <?= __('admin.support.index.filters.status.open'); ?>
                                        </option>
                                        <option value="in_progress" <?= ($filters['status'] ?? '') === 'in_progress' ? 'selected' : ''; ?>>
                                            <?= __('admin.support.index.filters.status.in_progress'); ?>
                                        </option>
                                        <option value="closed" <?= ($filters['status'] ?? '') === 'closed' ? 'selected' : ''; ?>>
                                            <?= __('admin.support.index.filters.status.closed'); ?>
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-2 d-flex justify-content-start justify-content-md-end">
                                    <button type="submit" class="btn btn-dark btn-sm w-100 w-md-auto">
                                        <i class="bi bi-funnel me-1"></i>
                                        <?= __('admin.support.index.filters.submit'); ?>
                                    </button>
                                </div>

                                <div class="col-12 col-md-3 text-md-end">
                                    <span class="small text-muted">
                                        <?= __('admin.support.index.filters.found'); ?>
                                        <?= (int)$totalTickets; ?>
                                    </span>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light small text-muted">
                                <tr>
                                    <th><?= __('admin.support.index.table.header.id'); ?></th>
                                    <th><?= __('admin.support.index.table.header.subject'); ?></th>
                                    <th><?= __('admin.support.index.table.header.customer'); ?></th>
                                    <th><?= __('admin.support.index.table.header.status'); ?></th>
                                    <th><?= __('admin.support.index.table.header.date'); ?></th>
                                    <th class="text-end"><?= __('admin.support.index.table.header.actions'); ?></th>
                                </tr>
                                </thead>
                                <tbody class="small">
                                <?php if (!empty($filteredTickets)): ?>
                                    <?php foreach ($filteredTickets as $ticket): ?>
                                        <tr>
                                            <td>#<?= (int)($ticket['id'] ?? 0); ?></td>
                                            <td style="max-width: 240px;">
                                                <div class="fw-semibold text-truncate">
                                                    <?= htmlspecialchars((string)($ticket['subject'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <?= htmlspecialchars((string)($ticket['customer_name'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                                <div class="text-muted">
                                                    <?= htmlspecialchars((string)($ticket['customer_email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php $statusVal = (string)($ticket['status'] ?? 'open'); ?>
                                                <?php
                                                $class = 'bg-secondary-subtle text-secondary';
                                                if ($statusVal === 'open') {
                                                    $class = 'bg-danger-subtle text-danger';
                                                } elseif ($statusVal === 'in_progress') {
                                                    $class = 'bg-warning-subtle text-warning';
                                                } elseif ($statusVal === 'closed') {
                                                    $class = 'bg-success-subtle text-success';
                                                }

                                                $statusKey   = 'admin.support.index.status.' . $statusVal;
                                                $statusLabel = __($statusKey);
                                                if ($statusLabel === $statusKey) {
                                                    $statusLabel = $statusVal;
                                                }
                                                ?>
                                                <span class="badge <?= $class; ?>">
                                                    <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars((string)($ticket['created_at'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="/admin/support/<?= (int)($ticket['id'] ?? 0); ?>"
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            <?= __('admin.support.index.empty'); ?>
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
