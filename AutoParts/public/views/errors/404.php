<?php
// public/views/errors/404.php
http_response_code(404);
?>
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">

                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
                             style="width:80px;height:80px;">
                            <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                        </div>
                    </div>

                    <p class="text-muted text-uppercase small mb-1">
                        <?= __('error.404.label'); ?>
                    </p>

                    <h1 class="display-5 fw-bold mb-2">
                        <?= __('error.404.code', '404'); ?>
                    </h1>

                    <h2 class="h5 fw-semibold mb-2">
                        <?= __('error.404.title'); ?>
                    </h2>

                    <p class="text-muted mb-4">
                        <?= __('error.404.text'); ?>
                    </p>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                        <a href="/" class="btn btn-warning text-dark fw-semibold px-4">
                            <i class="bi bi-house-door me-1"></i>
                            <?= __('error.404.btn_home'); ?>
                        </a>
                        <a href="/products" class="btn btn-outline-secondary fw-semibold px-4">
                            <i class="bi bi-grid-3x3-gap me-1"></i>
                            <?= __('error.404.btn_catalog'); ?>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
