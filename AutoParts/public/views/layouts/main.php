<!DOCTYPE html>
<html lang="<?= htmlspecialchars($locale ?? 'uk', ENT_QUOTES, 'UTF-8'); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'AutoParts', ENT_QUOTES, 'UTF-8'); ?></title>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- CSS Bootstrap 5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- JS Bootstrap 5.3.8 (bundle з Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e6ac00;
            border-color: #e6ac00;
            color: #fff;
        }
    </style>
</head>

<!-- ТУТ ГОЛОВНА ФІШКА: flex-колонка на всю висоту -->

<body class="bg-light d-flex flex-column min-vh-100">

<?php
include BASE_PATH . '/public/views/partials/header.php';
?>

<!-- MAIN: контент, який «штовхає» футер вниз -->
<main class="flex-shrink-0">
    <div class="container my-4">
        <?php if (!empty($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </div>
</main>

<!-- FOOTER: mt-auto «прижимає» його до низу -->
<footer class="mt-auto pt-5 pb-3 bg-dark text-light border-top">
    <div class="container">
        <div class="row gy-4">
            <div class="col-12 col-md-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-gear-wide-connected text-warning fs-2 me-2"></i>
                    <span class="fw-bold fs-4">
                        <?= htmlspecialchars(__('brand.shop_name', 'AutoParts'), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </div>
                <p class="small text-light mb-2">
                    <?= __('footer.about.line1'); ?>
                </p>
                <p class="small text-muted mb-0">
                    <?= __('footer.about.line2'); ?>
                </p>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <h5 class="fs-6 text-uppercase small mb-3"><?= __('footer.info.title'); ?></h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-1">
                        <a href="/info/about" class="text-decoration-none text-light">
                            <?= __('footer.info.about'); ?>
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="/info/faq" class="text-decoration-none text-light">
                            <?= __('footer.info.faq'); ?>
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="/info/payment-delivery" class="text-decoration-none text-light">
                            <?= __('footer.info.payment_delivery'); ?>
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="/info/contact" class="text-decoration-none text-light">
                            <?= __('footer.info.contact'); ?>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <h5 class="fs-6 text-uppercase small mb-3"><?= __('footer.legal.title'); ?></h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-1">
                        <a href="/info/privacy" class="text-decoration-none text-light">
                            <?= __('footer.legal.privacy'); ?>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <h5 class="fs-6 text-uppercase small mb-3"><?= __('footer.support.title'); ?></h5>
                <ul class="list-unstyled small mb-3">
                    <li class="mb-1 d-flex align-items-center">
                        <i class="bi bi-telephone me-2"></i>
                        <span>+38 (000) 000-00-00</span>
                    </li>
                    <li class="mb-1 d-flex align-items-center">
                        <i class="bi bi-envelope me-2"></i>
                        <span>support@autoparts.ua</span>
                    </li>
                    <li class="mb-1 d-flex align-items-center">
                        <i class="bi bi-clock me-2"></i>
                        <span><?= __('footer.support.hours'); ?></span>
                    </li>
                    <li class="mt-2">
                        <a href="/support" class="btn btn-sm btn-warning text-dark fw-semibold">
                            <i class="bi bi-chat-dots me-1"></i>
                            <?= __('footer.support.contact_us'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-top border-secondary mt-4 pt-3 d-flex flex-column align-items-center">
            <small class="text-white text-center mb-0">
                &copy; <?= date('Y'); ?> AutoParts. <?= __('footer.copy_rights'); ?>
            </small>
        </div>
    </div>
</footer>

</body>
</html>
