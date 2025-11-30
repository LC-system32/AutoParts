<?php
/**
 * @var string|null $flash
 * @var array<int,array<string,mixed>> $reviews
 */

$flash   = $flash   ?? null;
$reviews = $reviews ?? [];

$section = 'reviews';
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
                                    <i class="bi bi-chat-square-text fs-4 text-dark"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-1">
                                        <?= __('admin.reviews.pending.title', 'Відгуки на модерації'); ?>
                                    </h1>
                                    <p class="text-muted small mb-0">
                                        <?= __('admin.reviews.pending.subtitle', 'Перевірка, підтвердження або відхилення відгуків клієнтів.'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row gap-2">
                                <a href="/admin" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    <?= __('admin.reviews.pending.back_to_dashboard', 'На дашборд'); ?>
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

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light small text-muted">
                                <tr>
                                    <th><?= __('admin.reviews.pending.th_product', 'Товар'); ?></th>
                                    <th><?= __('admin.reviews.pending.th_customer', 'Клієнт'); ?></th>
                                    <th><?= __('admin.reviews.pending.th_rating', 'Рейтинг'); ?></th>
                                    <th><?= __('admin.reviews.pending.th_text', 'Текст'); ?></th>
                                    <th><?= __('admin.reviews.pending.th_date', 'Дата'); ?></th>
                                    <th class="text-end">
                                        <?= __('admin.reviews.pending.th_actions', 'Дії'); ?>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="small">
                                <?php if (!empty($reviews)): ?>
                                    <?php foreach ($reviews as $review): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">
                                                    <?= htmlspecialchars((string)($review['product_name'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                                <?php if (!empty($review['product_id'])): ?>
                                                    <a href="/admin/products/<?= (int)$review['product_id']; ?>/edit"
                                                       class="small text-decoration-none">
                                                        <?= __('admin.reviews.pending.link_product', 'Перейти до товару'); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div>
                                                    <?= htmlspecialchars((string)($review['user_name'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                                <div class="text-muted">
                                                    <?= htmlspecialchars((string)($review['user_email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-warning">
                                                    <?php $rating = (int)($review['rating'] ?? 0); ?>
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="bi <?= $i <= $rating ? 'bi-star-fill' : 'bi-star'; ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </td>
                                            <td style="max-width: 260px;">
                                                <?= nl2br(htmlspecialchars((string)($review['comment'] ?? ''), ENT_QUOTES, 'UTF-8')); ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars((string)($review['created_at'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td class="text-end">
                                                <form method="post"
                                                      action="/admin/reviews/<?= (int)($review['id'] ?? 0); ?>/moderate"
                                                      class="d-inline">
                                                    <?= \App\Core\Csrf::csrfInput(); ?>
                                                    <input type="hidden" name="decision" value="approve">
                                                    <button type="submit"
                                                            class="btn btn-success btn-sm mb-1"
                                                            title="<?= __('admin.reviews.pending.action_approve', 'Схвалити'); ?>">
                                                        <i class="bi bi-check2"></i>
                                                    </button>
                                                </form>
                                                <form method="post"
                                                      action="/admin/reviews/<?= (int)($review['id'] ?? 0); ?>/moderate"
                                                      class="d-inline">
                                                    <?= \App\Core\Csrf::csrfInput(); ?>
                                                    <input type="hidden" name="decision" value="reject">
                                                    <button type="submit"
                                                            class="btn btn-outline-danger btn-sm mb-1"
                                                            title="<?= __('admin.reviews.pending.action_reject', 'Відхилити'); ?>">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            <?= __('admin.reviews.pending.empty', 'Немає відгуків, що очікують модерації.'); ?>
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
