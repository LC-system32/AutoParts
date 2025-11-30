<?php

/** @var array<int, array<string, mixed>> $categories */
/** @var array<int, array<string, mixed>> $brands */
/** @var array<int, array<string, mixed>> $products */
/** @var array<int, array<string, mixed>> $topDeals */
/** @var array<int, array<string, mixed>> $popularProducts */
/** @var array<int, array<string, mixed>> $carMakes */
$carMakes = $carMakes ?? [];
?>

<!-- ГОЛОВНИЙ ПОШУК: ПІДБІР ПО АВТО (повна ширина) -->
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body bg-light">
            <div class="row g-3 align-items-center">

                <!-- Ліва частина: заголовок + опис -->
                <div class="col-12 col-lg-4 d-flex align-items-start">
                    <div>
                        <div class="text-uppercase small text-muted fw-semibold">
                            <?= __('home.main_search.badge'); ?>
                        </div>
                        <h1 class="h4 fw-bold mb-1">
                            <?= __('home.main_search.title'); ?>
                        </h1>
                        <p class="mb-0 small text-muted">
                            <?= __('home.main_search.subtitle'); ?>
                        </p>
                    </div>
                </div>

                <!-- Права частина: форма (марка → модель → пошук) -->
                <div class="col-12 col-lg-8">
                    <form class="row g-2 g-md-3 align-items-end"
                          method="get"
                          action="/products"
                          id="carPickerFormHome">

                        <!-- Марка (car_makes) + кнопка під полем -->
                        <div class="col-12 col-md-4 align-self-start">
                            <label for="carMakeHome" class="form-label small text-muted mb-1">
                                <?= __('home.main_search.label.make'); ?>
                            </label>
                            <select name="make_id" id="carMakeHome" class="form-select" required>
                                <option value="">
                                    <?= __('home.main_search.make.placeholder'); ?>
                                </option>
                                <?php foreach ($carMakes as $make): ?>
                                    <option value="<?= htmlspecialchars((string)($make['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                        <?= htmlspecialchars((string)($make['name'] ?? $make['slug'] ?? 'Без назви'), ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Кнопка розширеного підбору ПІД полем марки -->
                            <button type="button"
                                    class="btn btn-outline-secondary btn-sm w-100 mt-2 d-flex align-items-center justify-content-center"
                                    onclick="window.location.href='/car'">
                                <i class="bi bi-sliders me-1"></i>
                                <?= __('home.main_search.btn.advanced'); ?>
                            </button>
                        </div>

                        <!-- Модель (car_models) -->
                        <div class="col-12 col-md-4 align-self-start">
                            <label for="carModelHome" class="form-label small text-muted mb-1">
                                <?= __('home.main_search.label.model'); ?>
                            </label>
                            <select name="model_id" id="carModelHome" class="form-select" disabled required>
                                <option value="">
                                    <?= __('home.main_search.model.placeholder_initial'); ?>
                                </option>
                            </select>
                        </div>

                        <!-- Кнопка пошуку -->
                        <div class="col-12 col-md-4 align-self-start d-flex flex-column">
                            <label class="form-label small text-muted mb-1 d-none d-md-block">&nbsp;</label>
                            <button type="submit"
                                    class="btn btn-warning text-dark fw-semibold py-2"
                                    id="carPickerSubmitHome"
                                    disabled>
                                <i class="bi bi-search me-1"></i>
                                <?= __('home.main_search.btn.submit'); ?>
                            </button>
                            <div class="d-flex justify-content-lg-start justify-content-start mt-2 small text-muted">
                                <div class="me-3">
                                    <span class="badge bg-dark text-white me-1">1</span>
                                    <?= __('home.main_search.step1'); ?>
                                </div>
                                <div class="me-3">
                                    <span class="badge bg-secondary text-white me-1">2</span>
                                    <?= __('home.main_search.step2'); ?>
                                </div>
                                <div>
                                    <span class="badge bg-warning text-dark me-1">3</span>
                                    <?= __('home.main_search.step3'); ?>
                                </div>
                            </div>
                        </div>
                        <!-- Тип пошуку, щоб бекенд включив car-логіку -->
                        <input type="hidden" name="search_type" value="car">
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Головна: ліве вертикальне меню + правий hero-блок -->
<div class="row g-4 align-items-stretch">

    <!-- Ліва колонка: категорії -->
    <div class="col-12 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                <i class="bi bi-list-ul text-warning fs-4 me-2"></i>
                <div>
                    <div class="small text-muted text-uppercase">
                        <?= __('home.left_nav.badge'); ?>
                    </div>
                    <div class="fw-semibold">
                        <?= __('home.left_nav.title'); ?>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <?php
                // вертикальне меню root-категорій
                include BASE_PATH . '/public/views/partials/category-menu.php';
                ?>
            </div>
        </div>
    </div>

    <!-- Права колонка: hero + короткі переваги -->
    <div class="col-12 col-md-9 d-flex flex-column">

        <!-- Hero Carousel -->
        <div id="heroCarousel" class="carousel slide shadow-sm mb-3" data-bs-ride="carousel">
            <div class="carousel-inner rounded-4 overflow-hidden">

                <!-- Слайд 1 -->
                <div class="carousel-item active position-relative">
                    <img src="https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?auto=format&fit=crop&w=1200&q=80"
                         class="d-block w-100"
                         alt="<?= htmlspecialchars(__('home.hero.slide1.alt'), ENT_QUOTES, 'UTF-8'); ?>"
                         style="height:360px;object-fit:cover;">
                    <div class="position-absolute top-0 start-0 w-100 h-100"
                         style="background: linear-gradient(90deg, rgba(0,0,0,0.75), rgba(0,0,0,0.35), transparent);">
                    </div>
                    <div class="carousel-caption d-flex flex-column align-items-start justify-content-center h-100">
                        <span class="badge rounded-pill text-bg-warning text-dark mb-2">
                            <?= __('home.hero.slide1.badge'); ?>
                        </span>
                        <h2 class="fw-bold display-6 text-white mb-2">
                            <?= __('home.hero.slide1.title'); ?>
                        </h2>
                        <p class="text-light mb-3">
                            <?= __('home.hero.slide1.text'); ?>
                        </p>
                        <a href="/products" class="btn btn-warning text-dark fw-semibold px-4">
                            <?= __('home.hero.slide1.btn'); ?>
                        </a>
                    </div>
                </div>

                <!-- Слайд 2 -->
                <div class="carousel-item position-relative">
                    <img src="https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=1200&q=80"
                         class="d-block w-100"
                         alt="<?= htmlspecialchars(__('home.hero.slide2.alt'), ENT_QUOTES, 'UTF-8'); ?>"
                         style="height:360px;object-fit:cover;">
                    <div class="position-absolute top-0 start-0 w-100 h-100"
                         style="background: linear-gradient(90deg, rgba(0,0,0,0.75), rgba(0,0,0,0.35), transparent);">
                    </div>
                    <div class="carousel-caption d-flex flex-column align-items-start justify-content-center h-100">
                        <span class="badge rounded-pill text-bg-light text-dark mb-2">
                            <?= __('home.hero.slide2.badge'); ?>
                        </span>
                        <h2 class="fw-bold display-6 text-white mb-2">
                            <?= __('home.hero.slide2.title'); ?>
                        </h2>
                        <p class="text-light mb-3">
                            <?= __('home.hero.slide2.text'); ?>
                        </p>
                        <a href="/info/about" class="btn btn-outline-light fw-semibold px-4">
                            <?= __('home.hero.slide2.btn'); ?>
                        </a>
                    </div>
                </div>

                <!-- Слайд 3 -->
                <div class="carousel-item position-relative">
                    <img src="https://i.pinimg.com/736x/ae/8c/30/ae8c30e77a652fd4fb4882b18ccb64f7.jpg"
                         class="d-block w-100"
                         alt="<?= htmlspecialchars(__('home.hero.slide3.alt'), ENT_QUOTES, 'UTF-8'); ?>"
                         style="height:360px;object-fit:cover;">
                    <div class="position-absolute top-0 start-0 w-100 h-100"
                         style="background: linear-gradient(90deg, rgba(0,0,0,0.75), rgba(0,0,0,0.35), transparent);">
                    </div>
                    <div class="carousel-caption d-flex flex-column align-items-start justify-content-center h-100">
                        <span class="badge rounded-pill text-bg-danger mb-2">
                            <?= __('home.hero.slide3.badge'); ?>
                        </span>
                        <h2 class="fw-bold display-6 text-white mb-2">
                            <?= __('home.hero.slide3.title'); ?>
                        </h2>
                        <p class="text-light mb-3">
                            <?= __('home.hero.slide3.text'); ?>
                        </p>
                        <a href="/products?sort=popular" class="btn btn-warning text-dark fw-semibold px-4">
                            <?= __('home.hero.slide3.btn'); ?>
                        </a>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden"><?= __('carousel.prev'); ?></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden"><?= __('carousel.next'); ?></span>
            </button>
        </div>

        <!-- Короткі переваги під hero -->
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-shield-check text-success fs-3"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">
                                <?= __('home.features.quality.title'); ?>
                            </div>
                            <div class="small text-muted">
                                <?= __('home.features.quality.text'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-truck text-primary fs-3"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">
                                <?= __('home.features.delivery.title'); ?>
                            </div>
                            <div class="small text-muted">
                                <?= __('home.features.delivery.text'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-gear-wide-connected text-warning fs-3"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">
                                <?= __('home.features.by_car.title'); ?>
                            </div>
                            <div class="small text-muted">
                                <?= __('home.features.by_car.text'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Секція: Сьогоднішні топ-пропозиції -->
<section class="mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="badge rounded-pill text-bg-warning text-dark mb-1">
                <?= __('home.top_deals.badge'); ?>
            </span>
            <h2 class="h4 mb-1">
                <?= __('home.top_deals.title'); ?>
            </h2>
            <p class="small text-muted mb-0">
                <?= __('home.top_deals.text'); ?>
            </p>
        </div>
        <a href="/products?sort=discount"
           class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex align-items-center">
            <?= __('home.top_deals.btn_all'); ?>
            <i class="bi bi-arrow-right-short ms-1"></i>
        </a>
    </div>

    <div class="row row-cols-2 row-cols-md-4 g-4">
        <?php foreach ($topDeals as $deal): ?>
            <div class="col">
                <?php $product = $deal;
                include BASE_PATH . '/public/views/partials/product-card.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Секція: Популярні категорії -->
<section class="mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="badge rounded-pill text-bg-light mb-1">
                <i class="bi bi-ui-checks-grid me-1"></i>
                <?= __('home.categories.badge'); ?>
            </span>
            <h2 class="h4 mb-1">
                <?= __('home.categories.title'); ?>
            </h2>
            <p class="small text-muted mb-0">
                <?= __('home.categories.text'); ?>
            </p>
        </div>
        <a href="/categories"
           class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex align-items-center">
            <?= __('home.categories.btn_all'); ?>
            <i class="bi bi-arrow-right-short ms-1"></i>
        </a>
    </div>

    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
        <?php foreach (array_slice($categories, 0, 8) as $cat): ?>
            <div class="col">
                <a href="/categories/<?= htmlspecialchars((string)$cat['slug'], ENT_QUOTES, 'UTF-8'); ?>"
                   class="text-decoration-none">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3"
                                 style="width:56px;height:56px;">
                                <i class="bi bi-gear-fill text-warning fs-4"></i>
                            </div>
                            <h6 class="fw-semibold text-dark mb-1">
                                <?= htmlspecialchars((string)$cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </h6>
                            <?php if (!empty($cat['short_description'])): ?>
                                <p class="small text-muted mb-0">
                                    <?= htmlspecialchars((string)$cat['short_description'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Секція: Популярні товари -->
<section class="mt-5 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="badge rounded-pill text-bg-light mb-1">
                <i class="bi bi-stars me-1"></i>
                <?= __('home.popular.badge'); ?>
            </span>
            <h2 class="h4 mb-1">
                <?= __('home.popular.title'); ?>
            </h2>
            <p class="small text-muted mb-0">
                <?= __('home.popular.text'); ?>
            </p>
        </div>
        <a href="/products?sort=popular"
           class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex align-items-center">
            <?= __('home.popular.btn_all'); ?>
            <i class="bi bi-arrow-right-short ms-1"></i>
        </a>
    </div>

    <div class="row row-cols-2 row-cols-md-4 g-4">
        <?php foreach ($popularProducts as $product): ?>
            <div class="col">
                <?php include BASE_PATH . '/public/views/partials/product-card.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- JS: динамічне підвантаження моделей по вибраній марці (car_makes → car_models) -->
<script>
    const HOME_I18N = {
        modelPlaceholderInitial: <?= json_encode(__('home.car.models.placeholder_initial')); ?>,
        modelPlaceholderLoading: <?= json_encode(__('home.car.models.placeholder_loading')); ?>,
        modelPlaceholderChoose: <?= json_encode(__('home.car.models.placeholder_choose')); ?>,
        modelPlaceholderEmpty: <?= json_encode(__('home.car.models.placeholder_empty')); ?>,
        modelPlaceholderError: <?= json_encode(__('home.car.models.placeholder_error')); ?>,
        errorResponse: <?= json_encode(__('home.car.models.error_response')); ?>
    };

    document.addEventListener('DOMContentLoaded', function() {
        const makeSelectHome  = document.getElementById('carMakeHome');
        const modelSelectHome = document.getElementById('carModelHome');
        const submitHome      = document.getElementById('carPickerSubmitHome');

        if (!makeSelectHome || !modelSelectHome || !submitHome) return;

        function resetSelect(selectEl, placeholder, disabled = true) {
            selectEl.innerHTML = '<option value="">' + placeholder + '</option>';
            selectEl.disabled = disabled;
        }

        async function fetchJson(url) {
            const resp = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            if (!resp.ok) throw new Error(HOME_I18N.errorResponse);
            return await resp.json();
        }

        // Зміна марки → підтягуємо моделі через /car/models (CarController::modelsJson)
        makeSelectHome.addEventListener('change', async function() {
            const makeId = this.value;

            resetSelect(modelSelectHome, HOME_I18N.modelPlaceholderInitial, true);
            submitHome.disabled = true;

            if (!makeId) return;

            try {
                modelSelectHome.innerHTML = '<option value="">' + HOME_I18N.modelPlaceholderLoading + '</option>';
                modelSelectHome.disabled = true;

                const models = await fetchJson('/car/models?make_id=' + encodeURIComponent(makeId));

                resetSelect(modelSelectHome, HOME_I18N.modelPlaceholderChoose, false);

                if (Array.isArray(models) && models.length) {
                    models.forEach(function(m) {
                        const opt = document.createElement('option');
                        opt.value = m.id ?? '';
                        opt.textContent = m.name ?? (m.slug ?? 'Без назви');
                        modelSelectHome.appendChild(opt);
                    });
                } else {
                    resetSelect(modelSelectHome, HOME_I18N.modelPlaceholderEmpty, true);
                }
            } catch (e) {
                console.error(e);
                resetSelect(modelSelectHome, HOME_I18N.modelPlaceholderError, true);
            }
        });

        // Коли вибрана і марка, і модель – активуємо кнопку
        modelSelectHome.addEventListener('change', function() {
            if (makeSelectHome.value && modelSelectHome.value) {
                submitHome.disabled = false;
            } else {
                submitHome.disabled = true;
            }
        });
    });
</script>
