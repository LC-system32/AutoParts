<?php
/**
 * @var string|null $flash
 * @var array<int,array<string,mixed>> $orders   // усі замовлення (наприклад, з JSON/мока)
 * @var array<string,mixed> $filters
 * @var array<string,mixed> $pagination  (page, perPage, total, totalPages)
 */

$flash  = $flash  ?? null;
$orders = $orders ?? [];

// --- 1. Читаємо фільтри з GET ---
$filters = [
    'q'         => trim((string)($_GET['q'] ?? '')),
    'status'    => (string)($_GET['status'] ?? ''),
    'date_from' => (string)($_GET['date_from'] ?? ''),
    'date_to'   => (string)($_GET['date_to'] ?? ''),
];

// Валідація дат (примітивна, щоб не поламати фільтри)
foreach (['date_from', 'date_to'] as $key) {
    if ($filters[$key] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filters[$key])) {
        $filters[$key] = '';
    }
}

// --- 2. Пагінація (по масиву) ---
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;

// --- 3. Фільтрація масиву $orders ---
$filtered = array_filter($orders, function (array $order) use ($filters) {
    // 3.1 Пошук (q) по №, id, імені, телефону, email
    if ($filters['q'] !== '') {
        $haystack = mb_strtolower(
            (string)($order['id']            ?? '') . ' ' .
            (string)($order['order_number']  ?? '') . ' ' .
            (string)($order['customer_name'] ?? '') . ' ' .
            (string)($order['customer_phone']?? '') . ' ' .
            (string)($order['customer_email']?? ''),
            'UTF-8'
        );
        $needle = mb_strtolower($filters['q'], 'UTF-8');

        if (mb_strpos($haystack, $needle) === false) {
            return false;
        }
    }

    // 3.2 Статус
    if ($filters['status'] !== '') {
        $statusCode = (string)($order['status_code'] ?? '');
        if ($statusCode !== $filters['status']) {
            return false;
        }
    }

    // 3.3 Дата (беремо тільки YYYY-MM-DD з created_at)
    $orderDate = substr((string)($order['created_at'] ?? ''), 0, 10); // "2025-01-31"

    if ($filters['date_from'] !== '' && $orderDate !== '') {
        if ($orderDate < $filters['date_from']) {
            return false;
        }
    }

    if ($filters['date_to'] !== '' && $orderDate !== '') {
        if ($orderDate > $filters['date_to']) {
            return false;
        }
    }

    return true;
});

// --- 4. Порахували, порізали по сторінках ---
$total      = count($filtered);
$totalPages = max(1, (int)ceil($total / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

// тепер $orders – вже відфільтровані + нарізані під поточну сторінку
$orders = array_slice($filtered, $offset, $perPage);

$pagination = [
    'page'       => $page,
    'perPage'    => $perPage,
    'total'      => $total,
    'totalPages' => $totalPages,
];

$section = 'orders';

/**
 * Повернути локалізовану назву статусу замовлення
 */
function orderStatusLabel(string $status): string {
    $fallbacks = [
        'new'       => 'Нове',
        'pending'   => 'В обробці',
        'paid'      => 'Оплачене',
        'shipped'   => 'Відправлене',
        'completed' => 'Завершене',
        'cancelled' => 'Скасоване',
    ];
    $fallback = $fallbacks[$status] ?? $status;

    return __(
        'admin.orders.index.status.' . $status,
        $fallback
    );
}
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
                                    <i class="bi bi-receipt fs-4 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.orders.index.title', 'Замовлення'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.orders.index.subtitle', 'Облік і керування замовленнями клієнтів, статусами оплати та відправки.'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row gap-2">
                                <a href="/admin/orders?status=pending" class="btn btn-warning btn-sm text-dark">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    <?= __('admin.orders.index.button_new', 'Нові замовлення'); ?>
                                </a>
                                <a href="/admin/orders/export" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-filetype-csv me-1"></i>
                                    <?= __('admin.orders.index.button_export', 'Експорт'); ?>
                                </a>
                            </div>
                        </div>

                        <?php if (!empty($flash)): ?>
                            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="<?= htmlspecialchars(__('common.close', 'Закрити'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Фільтри -->
                        <form method="get" action="/admin/orders" class="border rounded-4 p-3 bg-light mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label class="form-label small text-muted mb-1">
                                        <?= __('admin.orders.index.filter.search_label', 'Пошук'); ?>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text"
                                               name="q"
                                               class="form-control border-start-0"
                                               placeholder="<?= __('admin.orders.index.filter.search_placeholder', '№ замовлення, імʼя, телефон, email'); ?>"
                                               value="<?= htmlspecialchars((string)$filters['q'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>

                                <div class="col-6 col-md-3">
                                    <label class="form-label small text-muted mb-1">
                                        <?= __('admin.orders.index.filter.status_label', 'Статус'); ?>
                                    </label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">
                                            <?= __('admin.orders.index.filter.status_all', 'Усі'); ?>
                                        </option>
                                        <option value="new"       <?= ($filters['status'] ?? '') === 'new'       ? 'selected' : ''; ?>>
                                            <?= __('admin.orders.index.status.new', 'Нове'); ?>
                                        </option>
                                        <option value="pending"   <?= ($filters['status'] ?? '') === 'pending'   ? 'selected' : ''; ?>>
                                            <?= __('admin.orders.index.status.pending', 'В обробці'); ?>
                                        </option>
                                        <option value="paid"      <?= ($filters['status'] ?? '') === 'paid'      ? 'selected' : ''; ?>>
                                            <?= __('admin.orders.index.status.paid', 'Оплачене'); ?>
                                        </option>
                                        <option value="shipped"   <?= ($filters['status'] ?? '') === 'shipped'   ? 'selected' : ''; ?>>
                                            <?= __('admin.orders.index.status.shipped', 'Відправлене'); ?>
                                        </option>
                                        <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>
                                            <?= __('admin.orders.index.status.completed', 'Завершене'); ?>
                                        </option>
                                        <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>
                                            <?= __('admin.orders.index.status.cancelled', 'Скасоване'); ?>
                                        </option>
                                    </select>
                                </div>

                                <div class="col-6 col-md-2">
                                    <label class="form-label small text-muted mb-1">
                                        <?= __('admin.orders.index.filter.date_from', 'З дати'); ?>
                                    </label>
                                    <input type="date"
                                           name="date_from"
                                           class="form-control form-control-sm"
                                           value="<?= htmlspecialchars((string)$filters['date_from'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="col-6 col-md-2">
                                    <label class="form-label small text-muted mb-1">
                                        <?= __('admin.orders.index.filter.date_to', 'По дату'); ?>
                                    </label>
                                    <input type="date"
                                           name="date_to"
                                           class="form-control form-control-sm"
                                           value="<?= htmlspecialchars((string)$filters['date_to'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="col-12 col-md-1 d-flex gap-2 justify-content-start justify-content-md-end">
                                    <button type="submit" class="btn btn-dark btn-sm w-100 w-md-auto">
                                        <i class="bi bi-funnel me-1"></i>
                                        <?= __('admin.orders.index.filter.submit', 'Ок'); ?>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Таблиця замовлень -->
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light small text-muted">
                                <tr>
                                    <th><?= __('admin.orders.index.th_number', '№'); ?></th>
                                    <th><?= __('admin.orders.index.th_customer', 'Клієнт'); ?></th>
                                    <th><?= __('admin.orders.index.th_total', 'Сума'); ?></th>
                                    <th><?= __('admin.orders.index.th_status', 'Статус'); ?></th>
                                    <th><?= __('admin.orders.index.th_payment', 'Оплата'); ?></th>
                                    <th><?= __('admin.orders.index.th_date', 'Дата'); ?></th>
                                    <th class="text-end"><?= __('admin.orders.index.th_actions', 'Дії'); ?></th>
                                </tr>
                                </thead>
                                <tbody class="small">
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <?php
                                        $id          = (string)($order['id'] ?? '');
                                        $orderNumber = (string)($order['order_number'] ?? $id);
                                        $status      = (string)($order['status_code'] ?? '');
                                        $paid        = $order['is_paid'] ?? false;
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="/admin/orders/<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>"
                                                   class="text-decoration-none">
                                                    <?= htmlspecialchars($orderNumber, ENT_QUOTES, 'UTF-8'); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">
                                                    <?= htmlspecialchars((string)($order['customer_name'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                                <div class="text-muted">
                                                    <?= htmlspecialchars((string)($order['customer_phone'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars((string)($order['total_amount'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                <?= htmlspecialchars((string)($order['currency'] ?? 'UAH'), ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = 'bg-light text-muted';
                                                if (in_array($status, ['new', 'pending'], true)) {
                                                    $statusClass = 'bg-warning-subtle text-warning';
                                                } elseif (in_array($status, ['paid', 'completed'], true)) {
                                                    $statusClass = 'bg-success-subtle text-success';
                                                } elseif ($status === 'shipped') {
                                                    $statusClass = 'bg-info-subtle text-info';
                                                } elseif ($status === 'cancelled') {
                                                    $statusClass = 'bg-secondary-subtle text-secondary';
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass; ?> text-uppercase">
                                                    <?= htmlspecialchars(orderStatusLabel($status), ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($paid): ?>
                                                    <span class="badge bg-success-subtle text-success">
                                                        <?= __('admin.orders.index.payment.paid', 'Оплачено'); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary">
                                                        <?= __('admin.orders.index.payment.unpaid', 'Не оплачено'); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars((string)($order['created_at'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="/admin/orders/<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>"
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">
                                            <?= __('admin.orders.index.empty', 'Замовлень не знайдено.'); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Пагінація -->
                        <?php if ($totalPages > 1): ?>
                            <nav class="mt-3">
                                <ul class="pagination pagination-sm mb-0">
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <?php
                                        $url = '/admin/orders?' . http_build_query(array_merge($filters, ['page' => $i]));
                                        ?>
                                        <li class="page-item<?= $i === $page ? ' active' : ''; ?>">
                                            <a class="page-link" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>">
                                                <?= $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>

                    </div>
                </div>
            </div><!-- /main -->
        </div>
    </div>
</section>
