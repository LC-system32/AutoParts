<?php
/** @var array<int, array<string,mixed>> $addresses */
/** @var string|null $flash */

$addresses = $addresses ?? [];
$flash     = $flash     ?? null;

$totalAddresses = count($addresses);
?>

<section class="py-4 py-md-5">

    <!-- HERO + КНОПКА ДОДАТИ -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="me-md-3 mb-3 mb-md-0">
                <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center"
                     style="width:72px;height:72px;">
                    <i class="bi bi-geo-alt fs-1 text-warning"></i>
                </div>
            </div>
            <div>
                <h1 class="fw-bold fs-3 mb-1">
                    <?= htmlspecialchars(__('page.addresses.title', 'Мої адреси'), ENT_QUOTES, 'UTF-8'); ?>
                </h1>
                <p class="text-muted mb-0">
                    <?= __('page.addresses.subtitle'); ?>
                </p>

                <?php if ($totalAddresses > 0): ?>
                    <div class="small text-muted mt-2">
                        <?= __('page.addresses.count.label'); ?>
                        <strong><?= $totalAddresses; ?></strong>
                        <?php if ($totalAddresses === 1): ?>
                            &middot; <?= __('page.addresses.count.single_hint'); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-3 mt-md-0">
            <a href="/profile/addresses/create" class="btn btn-warning text-dark fw-semibold">
                <i class="bi bi-plus-lg me-1"></i>
                <?= __('page.addresses.add_btn'); ?>
            </a>
        </div>
    </div>

    <?php if (!empty($flash)): ?>
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="<?= htmlspecialchars(__('ui.close', 'Закрити'), ENT_QUOTES, 'UTF-8'); ?>"></button>
        </div>
    <?php endif; ?>

    <?php if ($totalAddresses === 0): ?>

        <!-- ПУСТИЙ СТАН -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="mb-3">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
                         style="width:72px;height:72px;">
                        <i class="bi bi-house fs-1 text-warning"></i>
                    </div>
                </div>
                <h2 class="h5 fw-semibold mb-2">
                    <?= __('page.addresses.empty.title'); ?>
                </h2>
                <p class="text-muted mb-3">
                    <?= __('page.addresses.empty.text'); ?>
                </p>
                <a href="/profile/addresses/create" class="btn btn-warning text-dark fw-semibold">
                    <?= __('page.addresses.empty.add_first'); ?>
                </a>
            </div>
        </div>

    <?php else: ?>

        <!-- СІТКА КАРТОК АДРЕС -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-light border-0 rounded-top-4 py-3">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center me-2"
                              style="width:32px;height:32px;">
                            <i class="bi bi-journal-text text-warning"></i>
                        </span>
                        <div>
                            <span class="fw-semibold d-block">
                                <?= __('page.addresses.saved_block.title'); ?>
                            </span>
                            <span class="small text-muted">
                                <?= __('page.addresses.saved_block.subtitle'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="small text-muted mt-2 mt-md-0">
                        <?= __('page.addresses.saved_block.hint'); ?>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3 g-md-4">

                    <?php foreach ($addresses as $index => $addr): ?>
                        <?php
                        $fullName      = trim((string)($addr['full_name'] ?? ''));
                        $phone         = trim((string)($addr['phone'] ?? ''));
                        $country       = trim((string)($addr['country'] ?? ''));
                        $region        = trim((string)($addr['region'] ?? ''));
                        $city          = trim((string)($addr['city'] ?? ''));
                        $postalCode    = trim((string)($addr['postal_code'] ?? ''));
                        $street        = trim((string)($addr['street_address'] ?? ''));
                        $comment       = trim((string)($addr['comment'] ?? ''));

                        // Бейдж типу адреси (суто візуально)
                        $typeBadgeText  = '';
                        $typeBadgeClass = 'bg-light text-muted';

                        if ($index === 0) {
                            $typeBadgeText  = __('page.addresses.badge.primary');
                            $typeBadgeClass = 'bg-warning text-dark';
                        } elseif (stripos($comment, 'робота') !== false || stripos($comment, 'офіс') !== false) {
                            $typeBadgeText  = __('page.addresses.badge.work');
                            $typeBadgeClass = 'bg-primary-subtle text-primary';
                        } elseif (stripos($comment, 'сто') !== false) {
                            $typeBadgeText  = __('page.addresses.badge.service');
                            $typeBadgeClass = 'bg-success-subtle text-success';
                        }

                        $locationLine = trim(implode(', ', array_filter([$country, $region, $city])));
                        ?>

                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="border rounded-4 h-100 shadow-sm p-3 p-md-3 position-relative bg-white">

                                <!-- Верхній бейдж типу адреси -->
                                <?php if ($typeBadgeText !== ''): ?>
                                    <div class="position-absolute top-0 end-0 mt-2 me-2">
                                        <span class="badge <?= $typeBadgeClass; ?> rounded-pill small px-3">
                                            <i class="bi bi-pin-map me-1"></i>
                                            <?= htmlspecialchars($typeBadgeText, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex align-items-start mb-3">
                                    <div class="me-2">
                                        <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center"
                                             style="width:40px;height:40px;">
                                            <i class="bi bi-house-door text-warning"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            <?php if ($fullName !== ''): ?>
                                                <?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?>
                                            <?php else: ?>
                                                <?= __('page.addresses.recipient.unnamed'); ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($phone !== ''): ?>
                                            <div class="small text-muted">
                                                <i class="bi bi-telephone me-1"></i>
                                                <?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="small text-muted">
                                                <i class="bi bi-telephone me-1"></i>
                                                <?= __('page.addresses.phone.missing'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="small mb-2">
                                    <div class="text-muted mb-1">
                                        <?= __('page.addresses.location.label'); ?>
                                    </div>
                                    <?php if ($locationLine !== ''): ?>
                                        <div>
                                            <i class="bi bi-geo-alt me-1 text-warning"></i>
                                            <?= htmlspecialchars($locationLine, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            <?= __('page.addresses.location.missing'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="small mb-2">
                                    <div class="text-muted mb-1">
                                        <?= __('page.addresses.address.label'); ?>
                                    </div>
                                    <?php if ($street !== ''): ?>
                                        <div>
                                            <i class="bi bi-signpost me-1 text-warning"></i>
                                            <?= htmlspecialchars($street, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted">
                                            <i class="bi bi-signpost me-1"></i>
                                            <?= __('page.addresses.address.missing'); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($postalCode !== ''): ?>
                                        <div class="mt-1">
                                            <span class="badge bg-light text-muted rounded-pill small">
                                                <?= __('page.addresses.postal_code.badge'); ?>
                                                <?= htmlspecialchars($postalCode, ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($comment !== ''): ?>
                                    <div class="small text-muted mb-3">
                                        <div class="text-muted mb-1">
                                            <?= __('page.addresses.comment.label'); ?>
                                        </div>
                                        <div class="border-start ps-2">
                                            <?= nl2br(htmlspecialchars($comment, ENT_QUOTES, 'UTF-8')); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                                    <div class="d-flex gap-2">
                                        <a href="/profile/addresses/<?= (int)$addr['id']; ?>/edit"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                            <span class="d-none d-md-inline ms-1">
                                                <?= __('page.addresses.actions.edit'); ?>
                                            </span>
                                        </a>
                                        <form action="/profile/addresses/<?= (int)$addr['id']; ?>/delete"
                                              method="post"
                                              class="d-inline-block"
                                              onsubmit="return confirm(<?= json_encode(__('page.addresses.actions.delete_confirm')); ?>);">
                                            <input type="hidden" name="_csrf"
                                                   value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8'); ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                                <span class="d-none d-md-inline ms-1">
                                                    <?= __('page.addresses.actions.delete'); ?>
                                                </span>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>

    <?php endif; ?>

</section>
