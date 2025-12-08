<?php

/**
 * @var string|null $flash
 * @var array<string,mixed> $stats
 * @var array<int,array<string,mixed>> $recentOrders      (опційно)
 * @var array<int,array<string,mixed>> $pendingReviews    (опційно)
 * @var array<int,array<string,mixed>> $openTickets       (опційно)
 */

$stats          = $stats ?? [];
$recentOrders   = $recentOrders   ?? [];
$pendingReviews = $pendingReviews ?? [];
$openTickets    = $openTickets    ?? [];

$currentPeriod = $_GET['period'] ?? 'today';

function isPeriod(string $current, string $period): bool
{
    return $current === $period;
}
?>

<section class="py-3 py-md-4">
    <div class="container-fluid">
        <div class="row">
            <?php include '_sidebar.php'; ?>

            <!-- Основний контент -->
            <div class="col-12 col-lg-9 col-xl-10">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body p-3 p-md-4">

                        <!-- Верхній бар: заголовок + пошук + "швидкі" кнопки -->
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center me-3"
                                     style="width:56px;height:56px;">
                                    <i class="bi bi-speedometer2 fs-3 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.dashboard.title', 'Адмін-панель AutoParts'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.dashboard.subtitle', 'Керування користувачами, замовленнями, каталогом, відгуками, підтримкою та маркетингом.'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2">
                                <form class="input-group input-group-sm" method="get" action="/admin/orders">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text"
                                           name="q"
                                           class="form-control border-start-0"
                                           placeholder="<?= __('admin.dashboard.search_orders_placeholder', 'Пошук замовлення / клієнта'); ?>">
                                </form>
                                <a href="/admin/orders?status=pending"
                                   class="btn btn-warning btn-sm text-dark fw-semibold">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    <?= __('admin.dashboard.new_orders_button', 'Нові замовлення'); ?>
                                </a>
                            </div>
                        </div>

                        <!-- Flash -->
                        <?php if (!empty($flash)): ?>
                            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="<?= htmlspecialchars(__('common.close', 'Закрити'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Верхній ряд метрик (клієнти, замовлення, оборот, підтримка) -->
                        <div class="row g-2 g-md-3 mb-3">
                            <div class="col-6 col-md-3">
                                <div class="border rounded-4 px-3 py-2 bg-light h-100">
                                    <div class="small text-muted d-flex justify-content-between align-items-center">
                                        <span><?= __('admin.dashboard.card_users_title', 'Користувачі'); ?></span>
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="fw-semibold fs-5">
                                        <?= htmlspecialchars((string)($stats['users_count'] ?? '—')); ?>
                                    </div>
                                    <div class="small text-success">
                                        <?= htmlspecialchars((string)($stats['users_new_today'] ?? '')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="border rounded-4 px-3 py-2 bg-light h-100">
                                    <div class="small text-muted d-flex justify-content-between align-items-center">
                                        <span><?= __('admin.dashboard.card_orders_today_title', 'Замовлення (сьогодні)'); ?></span>
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                    <div class="fw-semibold fs-5">
                                        <?= htmlspecialchars((string)($stats['orders_today'] ?? ($stats['orders_count'] ?? '—'))); ?>
                                    </div>
                                    <div class="small text-warning">
                                        <?= __('admin.dashboard.card_orders_in_progress', 'В роботі:'); ?>
                                        <?= htmlspecialchars((string)($stats['pending_orders'] ?? '0')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="border rounded-4 px-3 py-2 bg-light h-100">
                                    <div class="small text-muted d-flex justify-content-between align-items-center">
                                        <span><?= __('admin.dashboard.card_turnover_today_title', 'Оборот (сьогодні)'); ?></span>
                                        <i class="bi bi-cash-stack"></i>
                                    </div>
                                    <div class="fw-semibold fs-5">
                                        <?= htmlspecialchars((string)($stats['sales_today'] ?? '—')); ?>
                                    </div>
                                    <div class="small text-muted">
                                        <?= __('admin.dashboard.card_turnover_avg_check', 'Середній чек:'); ?>
                                        <?= htmlspecialchars((string)($stats['avg_order_value'] ?? '—')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="border rounded-4 px-3 py-2 bg-light h-100">
                                    <div class="small text-muted d-flex justify-content-between align-items-center">
                                        <span><?= __('admin.dashboard.card_reviews_support_title', 'Відгуки та підтримка'); ?></span>
                                        <i class="bi bi-chat-square-text"></i>
                                    </div>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars((string)($stats['pending_reviews'] ?? '0')); ?>
                                        <span class="small text-muted">
                                            <?= __('admin.dashboard.card_reviews_on_moderation', 'на модерації'); ?>
                                        </span>
                                    </div>
                                    <div class="small text-danger">
                                        <?= __('admin.dashboard.card_reviews_open_tickets', 'Відкриті тікети:'); ?>
                                        <?= htmlspecialchars((string)($stats['open_tickets'] ?? '0')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Другий ряд метрик: каталог та маркетинг -->
                        <div class="row g-2 g-md-3 mb-3">
                            <div class="col-6 col-md-3">
                                <div class="border rounded-4 px-3 py-2 bg-white h-100">
                                    <div class="small text-muted d-flex justify-content-between align-items-center">
                                        <span><?= __('admin.dashboard.card_products_title', 'Товари'); ?></span>
                                        <i class="bi bi-boxes"></i>
                                    </div>
                                    <div class="fw-semibold fs-5">
                                        <?= htmlspecialchars((string)($stats['products_count'] ?? '—')); ?>
                                    </div>
                                    <div class="small text-muted">
                                        <?= __('admin.dashboard.card_products_active', 'Активні:'); ?>
                                        <?= htmlspecialchars((string)($stats['products_active'] ?? '—')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="border rounded-4 px-3 py-2 bg-white h-100">
                                    <div class="small text-muted d-flex justify-content-between align-items-center">
                                        <span><?= __('admin.dashboard.card_categories_title', 'Категорії'); ?></span>
                                        <i class="bi bi-grid-3x3-gap"></i>
                                    </div>
                                    <div class="fw-semibold fs-5">
                                        <?= htmlspecialchars((string)($stats['categories_count'] ?? '—')); ?>
                                    </div>
                                    <div class="small text-muted">
                                        <?= __('admin.dashboard.card_categories_top', 'Топова:'); ?>
                                        <?= htmlspecialchars((string)($stats['top_category_name'] ?? '—')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="border rounded-4 px-3 py-2 bg-white h-100">
                                    <div class="small text-muted d-flex justify-content-between align-items-center">
                                        <span><?= __('admin.dashboard.card_brands_title', 'Бренди'); ?></span>
                                        <i class="bi bi-badge-tm"></i>
                                    </div>
                                    <div class="fw-semibold fs-5">
                                        <?= htmlspecialchars((string)($stats['brands_count'] ?? '—')); ?>
                                    </div>
                                    <div class="small text-muted">
                                        <?= __('admin.dashboard.card_brands_top', 'Популярний:'); ?>
                                        <?= htmlspecialchars((string)($stats['top_brand_name'] ?? '—')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="border rounded-4 px-3 py-2 bg-white h-100">
                                    <div class="small text-muted d-flex justify-content-between align-items-center">
                                        <span><?= __('admin.dashboard.card_marketing_title', 'Маркетинг'); ?></span>
                                        <i class="bi bi-megaphone"></i>
                                    </div>
                                    <div class="fw-semibold">
                                        <?= __('admin.dashboard.card_marketing_coupons', 'Купони:'); ?>
                                        <?= htmlspecialchars((string)($stats['active_coupons'] ?? '0')); ?>
                                    </div>
                                    <div class="small text-success">
                                        <?= __('admin.dashboard.card_marketing_campaigns', 'Активні кампанії:'); ?>
                                        <?= htmlspecialchars((string)($stats['active_campaigns'] ?? '0')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Блок "Огляд замовлень" + картки по відгуках -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-xl-8">
                                <div class="border rounded-4 p-3 h-100 bg-white">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                                        <div>
                                            <div class="small text-muted text-uppercase fw-semibold">
                                                <?= __('admin.dashboard.orders_overview.title', 'Огляд замовлень'); ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?= __('admin.dashboard.orders_overview.subtitle', 'Розподіл за статусами (умовна візуалізація)'); ?>
                                            </div>
                                        </div>

                                        <!-- Вибір періоду для дашборду -->
                                        <form method="get" action="/admin" class="d-flex align-items-center gap-2 small">
                                            <label for="period" class="text-muted mb-0">
                                                <?= __('admin.dashboard.orders_overview.period_label', 'Період:'); ?>
                                            </label>
                                            <select id="period" name="period" class="form-select form-select-sm"
                                                    onchange="this.form.submit()">
                                                <option value="today" <?= isPeriod($currentPeriod, 'today') ? 'selected' : ''; ?>>
                                                    <?= __('admin.dashboard.orders_overview.period.today', 'Сьогодні'); ?>
                                                </option>
                                                <option value="week" <?= isPeriod($currentPeriod, 'week') ? 'selected' : ''; ?>>
                                                    <?= __('admin.dashboard.orders_overview.period.week', '7 днів'); ?>
                                                </option>
                                                <option value="month" <?= isPeriod($currentPeriod, 'month') ? 'selected' : ''; ?>>
                                                    <?= __('admin.dashboard.orders_overview.period.month', '30 днів'); ?>
                                                </option>
                                                <option value="all" <?= isPeriod($currentPeriod, 'all') ? 'selected' : ''; ?>>
                                                    <?= __('admin.dashboard.orders_overview.period.all', 'За весь час'); ?>
                                                </option>
                                            </select>
                                        </form>
                                    </div>

                                    <?php
                                    $totalToday = (int)($stats['orders_today'] ?? 0);
                                    $pending    = (int)($stats['orders_pending_today'] ?? ($stats['pending_orders'] ?? 0));
                                    $paid       = (int)($stats['orders_paid_today'] ?? 0);
                                    $shipped    = (int)($stats['orders_shipped_today'] ?? 0);

                                    $sum = max($pending + $paid + $shipped, 1);
                                    $pctPending = round($pending / $sum * 100);
                                    $pctPaid    = round($paid    / $sum * 100);
                                    $pctShipped = round($shipped / $sum * 100);

                                    $periodLabel = match ($currentPeriod) {
                                        'week'  => __('admin.dashboard.orders_overview.total_week', 'за 7 днів'),
                                        'month' => __('admin.dashboard.orders_overview.total_month', 'за 30 днів'),
                                        'all'   => __('admin.dashboard.orders_overview.total_all', 'за весь час'),
                                        default => __('admin.dashboard.orders_overview.total_today', 'за сьогодні'),
                                    };
                                    ?>
                                    <div class="mb-2 small text-muted">
                                        <?= __('admin.dashboard.orders_overview.total_prefix', 'Всього замовлень :period:'); ?>
                                        <span class="fw-semibold">
                                            <?= htmlspecialchars((string)$totalToday); ?>
                                        </span>
                                        <span class="ms-1">
                                            (<?= htmlspecialchars($periodLabel, ENT_QUOTES, 'UTF-8'); ?>)
                                        </span>
                                    </div>

                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span><?= __('admin.dashboard.orders_overview.status_pending', 'В обробці'); ?></span>
                                            <span><?= $pending; ?></span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-warning" style="width: <?= $pctPending; ?>%;"></div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span><?= __('admin.dashboard.orders_overview.status_paid', 'Оплачені'); ?></span>
                                            <span><?= $paid; ?></span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: <?= $pctPaid; ?>%;"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span><?= __('admin.dashboard.orders_overview.status_shipped', 'Відправлені'); ?></span>
                                            <span><?= $shipped; ?></span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-info" style="width: <?= $pctShipped; ?>%;"></div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mt-3 small">
                                        <a href="/admin/orders?status=pending" class="btn btn-outline-warning btn-sm">
                                            <i class="bi bi-hourglass-split me-1"></i>
                                            <?= __('admin.dashboard.orders_overview.btn_pending', 'В обробці'); ?>
                                        </a>
                                        <a href="/admin/orders?status=paid" class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-check2-circle me-1"></i>
                                            <?= __('admin.dashboard.orders_overview.btn_paid', 'Оплачені'); ?>
                                        </a>
                                        <a href="/admin/orders?status=shipped" class="btn btn-outline-info btn-sm">
                                            <i class="bi bi-truck me-1"></i>
                                            <?= __('admin.dashboard.orders_overview.btn_shipped', 'Відправлені'); ?>
                                        </a>
                                        <a href="/admin/orders/export?period=<?= htmlspecialchars($currentPeriod, ENT_QUOTES, 'UTF-8'); ?>"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-filetype-csv me-1"></i>
                                            <?= __('admin.dashboard.orders_overview.btn_export', 'Експорт звіту'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-xl-4">
                                <div class="border rounded-4 p-3 h-100 bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-muted text-uppercase fw-semibold">
                                            <?= __('admin.dashboard.reviews.block_title', 'Відгуки'); ?>
                                        </span>
                                        <a href="/admin/reviews/pending" class="small text-decoration-none">
                                            <?= __('admin.dashboard.reviews.link_all', 'Всі'); ?>
                                            <i class="bi bi-arrow-right-short"></i>
                                        </a>
                                    </div>

                                    <div class="mb-2">
                                        <div class="small text-muted">
                                            <?= __('admin.dashboard.reviews.avg_rating', 'Середній рейтинг'); ?>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="fs-4 fw-semibold me-2">
                                                <?= htmlspecialchars((string)($stats['avg_rating'] ?? '—')); ?>
                                            </span>
                                            <div class="text-warning">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-half"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="small text-muted">
                                            <?= __('admin.dashboard.reviews.total', 'Всього відгуків'); ?>
                                        </div>
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars((string)($stats['reviews_count'] ?? '—')); ?>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="small text-muted">
                                            <?= __('admin.dashboard.reviews.on_moderation', 'На модерації'); ?>
                                        </div>
                                        <div class="fw-semibold text-warning">
                                            <?= htmlspecialchars((string)($stats['pending_reviews'] ?? '0')); ?>
                                        </div>
                                    </div>

                                    <hr class="my-2">

                                    <div class="small text-muted mb-1">
                                        <?= __('admin.dashboard.reviews.latest_short', 'Останні відгуки (коротко)'); ?>
                                    </div>

                                    <?php if (!empty($pendingReviews)): ?>
                                        <div class="list-group list-group-flush small">
                                            <?php foreach (array_slice($pendingReviews, 0, 3) as $review): ?>
                                                <div class="list-group-item px-0 py-1 border-0 d-flex justify-content-between align-items-center">
                                                    <div class="me-2">
                                                        <div class="fw-semibold text-truncate" style="max-width: 160px;">
                                                            <?= htmlspecialchars((string)($review['product_name'] ?? __('admin.dashboard.reviews.product_fallback', 'Товар'))); ?>
                                                        </div>
                                                        <div class="text-muted">
                                                            <?= htmlspecialchars((string)($review['user_name'] ?? __('admin.dashboard.reviews.customer_fallback', 'Клієнт'))); ?>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="text-warning">
                                                            <i class="bi bi-star-fill"></i>
                                                            <?= htmlspecialchars((string)($review['rating'] ?? '')); ?>
                                                        </div>
                                                        <a href="/admin/reviews/pending" class="small text-decoration-none">
                                                            <?= __('admin.dashboard.reviews.moderate', 'Модерувати'); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="small text-muted mb-0">
                                            <?= __('admin.dashboard.reviews.empty', 'Немає відгуків, що очікують модерації.'); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Останні замовлення + Тікети підтримки -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-xl-7">
                                <div class="border rounded-4 p-3 h-100 bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-muted text-uppercase fw-semibold">
                                            <?= __('admin.dashboard.recent_orders.block_title', 'Останні замовлення'); ?>
                                        </span>
                                        <a href="/admin/orders" class="small text-decoration-none">
                                            <?= __('admin.dashboard.recent_orders.link_all', 'Всі замовлення'); ?>
                                            <i class="bi bi-arrow-right-short"></i>
                                        </a>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-muted">
                                                    <th scope="col"><?= __('admin.dashboard.recent_orders.th_number', '№'); ?></th>
                                                    <th scope="col"><?= __('admin.dashboard.recent_orders.th_customer', 'Клієнт'); ?></th>
                                                    <th scope="col"><?= __('admin.dashboard.recent_orders.th_total', 'Сума'); ?></th>
                                                    <th scope="col"><?= __('admin.dashboard.recent_orders.th_status', 'Статус'); ?></th>
                                                    <th scope="col"><?= __('admin.dashboard.recent_orders.th_date', 'Дата'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody class="small">
                                                <?php if (!empty($recentOrders)): ?>
                                                    <?php foreach ($recentOrders as $order): ?>
                                                        <tr>
                                                            <td>
                                                                <a href="/admin/orders/<?= htmlspecialchars((string)($order['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                                                   class="text-decoration-none">
                                                                    <?= htmlspecialchars((string)($order['order_number'] ?? $order['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <?= htmlspecialchars((string)($order['customer_name'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                            </td>
                                                            <td>
                                                                <?= htmlspecialchars((string)($order['total_amount'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                                <?= htmlspecialchars((string)($order['currency'] ?? 'UAH'), ENT_QUOTES, 'UTF-8'); ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-light text-muted text-uppercase">
                                                                    <?= htmlspecialchars((string)($order['status_code'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?= htmlspecialchars((string)($order['created_at'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted py-3">
                                                            <?= __('admin.dashboard.recent_orders.empty', 'Немає останніх замовлень для відображення.'); ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-xl-5">
                                <div class="border rounded-4 p-3 h-100 bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-muted text-uppercase fw-semibold">
                                            <?= __('admin.dashboard.tickets.block_title', 'Тікети підтримки'); ?>
                                        </span>
                                        <a href="/admin/support?status=open" class="small text-decoration-none">
                                            <?= __('admin.dashboard.tickets.link_all', 'Всі тікети'); ?>
                                            <i class="bi bi-arrow-right-short"></i>
                                        </a>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light small text-muted">
                                                <tr>
                                                    <th><?= __('admin.dashboard.tickets.th_id', 'ID'); ?></th>
                                                    <th><?= __('admin.dashboard.tickets.th_subject', 'Тема'); ?></th>
                                                    <th><?= __('admin.dashboard.tickets.th_status', 'Статус'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody class="small">
                                                <?php if (!empty($openTickets)): ?>
                                                    <?php foreach (array_slice($openTickets, 0, 5) as $ticket): ?>
                                                        <tr>
                                                            <td>#<?= htmlspecialchars((string)($ticket['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                            <td class="text-truncate" style="max-width: 160px;">
                                                                <?= htmlspecialchars((string)($ticket['subject'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-danger-subtle text-danger text-capitalize">
                                                                    <?= htmlspecialchars((string)($ticket['status'] ?? 'open'), ENT_QUOTES, 'UTF-8'); ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted py-3">
                                                            <?= __('admin.dashboard.tickets.empty', 'Немає відкритих тікетів підтримки.'); ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <hr class="my-3">

                                    <div class="small text-muted mb-1">
                                        <?= __('admin.dashboard.stock.block_title', 'Запаси та склади'); ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small mb-1">
                                        <span><?= __('admin.dashboard.stock.low_stock_label', 'Позицій з малим залишком'); ?></span>
                                        <span class="fw-semibold text-danger">
                                            <?= htmlspecialchars((string)($stats['low_stock_offers'] ?? '0')); ?>
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-danger"
                                             style="width: <?= min(100, (int)($stats['low_stock_percent'] ?? 0)); ?>%;"></div>
                                    </div>
                                    <div class="mt-2 small">
                                        <a href="/admin/stock?filter=low" class="text-decoration-none">
                                            <?= __('admin.dashboard.stock.link_low_stock', 'Переглянути товари з низьким залишком'); ?>
                                            <i class="bi bi-arrow-right-short"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Новий блок: Завдання менеджера + Швидкі секції каталогу/маркетингу -->
                        <div class="row g-3 mb-2">
                            <!-- Завдання менеджера на сьогодні -->
                            <div class="col-12 col-lg-4">
                                <div class="border rounded-4 p-3 h-100 bg-white">
                                    <div class="small text-muted text-uppercase fw-semibold mb-2">
                                        <?= __('admin.dashboard.tasks.block_title', 'Завдання менеджера (сьогодні)'); ?>
                                    </div>
                                    <ul class="list-group list-group-flush small">
                                        <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="bi bi-hourglass-split me-1 text-warning"></i>
                                                <?= __('admin.dashboard.tasks.process_orders', 'Обробити замовлення'); ?>
                                            </span>
                                            <span class="badge bg-warning-subtle text-warning">
                                                <?= htmlspecialchars((string)($stats['pending_orders'] ?? '0')); ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="bi bi-chat-left-text me-1 text-info"></i>
                                                <?= __('admin.dashboard.tasks.moderate_reviews', 'Модерувати відгуки'); ?>
                                            </span>
                                            <span class="badge bg-info-subtle text-info">
                                                <?= htmlspecialchars((string)($stats['pending_reviews'] ?? '0')); ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="bi bi-life-preserver me-1 text-danger"></i>
                                                <?= __('admin.dashboard.tasks.close_tickets', 'Закрити тікети'); ?>
                                            </span>
                                            <span class="badge bg-danger-subtle text-danger">
                                                <?= htmlspecialchars((string)($stats['open_tickets'] ?? '0')); ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="bi bi-box-seam me-1 text-danger"></i>
                                                <?= __('admin.dashboard.tasks.check_stock', 'Перевірити залишки'); ?>
                                            </span>
                                            <span class="badge bg-danger-subtle text-danger">
                                                <?= htmlspecialchars((string)($stats['low_stock_offers'] ?? '0')); ?>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Швидкий доступ до каталогу -->
                            <div class="col-12 col-lg-4">
                                <div class="border rounded-4 p-3 h-100 bg-white">
                                    <div class="small text-muted text-uppercase fw-semibold mb-2">
                                        <?= __('admin.dashboard.catalog_quick.block_title', 'Каталог: швидкі дії'); ?>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 small">
                                        <a href="/admin/products/create" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            <?= __('admin.dashboard.catalog_quick.add_product', 'Додати товар'); ?>
                                        </a>
                                        <a href="/admin/categories" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-grid-3x3-gap me-1"></i>
                                            <?= __('admin.dashboard.catalog_quick.categories', 'Категорії'); ?>
                                        </a>
                                        <a href="/admin/brands" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-badge-tm me-1"></i>
                                            <?= __('admin.dashboard.catalog_quick.brands', 'Бренди'); ?>
                                        </a>
                                        <a href="/admin/stock" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-box-seam me-1"></i>
                                            <?= __('admin.dashboard.catalog_quick.stock', 'Управління запасами'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Швидкий доступ до маркетингу та контенту -->
                            <div class="col-12 col-lg-4">
                                <div class="border rounded-4 p-3 h-100 bg-white">
                                    <div class="small text-muted text-uppercase fw-semibold mb-2">
                                        <?= __('admin.dashboard.marketing_quick.block_title', 'Маркетинг та контент'); ?>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 small">
                                        <a href="/admin/discounts" class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-percent me-1"></i>
                                            <?= __('admin.dashboard.marketing_quick.create_discount', 'Створити знижку'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /card-body -->
                </div><!-- /card -->
            </div><!-- /col main -->
        </div><!-- /row -->
    </div><!-- /container-fluid -->
</section>
