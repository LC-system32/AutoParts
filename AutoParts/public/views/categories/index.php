<?php
// file: public/views/categories/index.php

/** @var array<int, array<string, mixed>> $categories */

// Безпечний to-lower з fallback
$lower = function (string $s): string {
    return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
};

// Вхідні параметри
$q       = trim((string)($_GET['q'] ?? ''));
$sort    = (string)($_GET['sort'] ?? 'name_asc');
$perPage = (int)($_GET['per_page'] ?? 24);
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = in_array($perPage, [12,24,36,48], true) ? $perPage : 24;

// 1) Фільтрація по пошуку
$filtered = array_filter($categories, function (array $cat) use ($q, $lower) {
    if ($q === '') return true;
    $haystack = $lower((string)($cat['name'] ?? '') . ' ' . (string)($cat['description'] ?? ''));
    $needle   = $lower($q);
    return strpos($haystack, $needle) !== false;
});

// 2) Сортування
usort($filtered, function (array $a, array $b) use ($sort, $lower) {
    $nameA  = $lower((string)($a['name'] ?? ''));
    $nameB  = $lower((string)($b['name'] ?? ''));
    $countA = (int)($a['products_count'] ?? 0);
    $countB = (int)($b['products_count'] ?? 0);

    switch ($sort) {
        case 'name_desc':
            return $nameB <=> $nameA;
        case 'products_desc':
            $cmp = $countB <=> $countA;
            return $cmp !== 0 ? $cmp : ($nameA <=> $nameB);
        case 'name_asc':
        default:
            return $nameA <=> $nameB;
    }
});

// 3) Пагінація
$totalCategories = count($filtered);
$totalPages      = max(1, (int)ceil($totalCategories / max(1, $perPage)));
$page            = min($page, $totalPages);
$offset          = ($page - 1) * $perPage;
$pageItems       = array_slice($filtered, $offset, $perPage);

// URL builder з поточними параметрами
$buildUrl = function(array $overrides = []): string {
    $query = array_merge($_GET, $overrides);
    return '/categories?' . http_build_query($query);
};
?>

<section class="mb-4">

  <!-- HERO -->
  <div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body px-3 px-md-4 py-3 py-md-4">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
              <li class="breadcrumb-item">
                <a href="/" class="text-decoration-none text-muted">
                  <?= __('common.home'); ?>
                </a>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                  <?= __('categories.index.breadcrumb'); ?>
              </li>
            </ol>
          </nav>

          <h1 class="h4 h3-md mb-1 fw-semibold">
              <?= __('categories.index.title'); ?>
          </h1>
          <p class="text-muted mb-0">
              <?= __('categories.index.subtitle'); ?>
          </p>
        </div>

        <div class="text-md-end">
          <div class="d-inline-flex flex-column align-items-md-end gap-1">
            <span class="badge rounded-pill text-bg-warning text-dark fw-semibold px-3 py-2">
              <i class="bi bi-grid-3x3-gap me-1"></i>
              <?= __('categories.index.badge.on_page'); ?>: <?= count($pageItems); ?>
            </span>
            <span class="small text-muted">
              <?= __('categories.index.badge.total'); ?>:
              <span class="fw-semibold"><?= $totalCategories; ?></span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- LAYOUT -->
  <div class="row g-3 g-md-4">

    <!-- LEFT: FILTERS -->
    <div class="col-12 col-lg-3">
      <form id="filtersForm"
            class="card border-0 shadow-sm rounded-4 mb-3 mb-lg-0"
            method="get"
            action="/categories">
        <div class="card-body p-3 p-md-4">

          <!-- Пошук -->
          <div class="mb-3">
            <label class="form-label small text-muted mb-1">
              <?= __('categories.filters.search.label'); ?>
            </label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
              </span>
              <input
                type="text"
                class="form-control border-start-0"
                name="q"
                placeholder="<?= __('categories.filters.search.placeholder'); ?>"
                value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>"
              >
            </div>
          </div>

          <!-- К-сть на сторінці -->
          <div class="mb-3">
            <label class="form-label small text-muted mb-1">
              <?= __('catalog.filters.per_page.label'); ?>
            </label>
            <select class="form-select form-select-sm" name="per_page">
              <?php foreach ([12,24,36,48] as $n): ?>
                <option value="<?= $n; ?>" <?= $perPage === $n ? 'selected' : ''; ?>>
                    <?= $n; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Поточне сортування як hidden -->
          <input type="hidden" name="sort" value="<?= htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>">

          <!-- Кнопки -->
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-warning text-dark fw-semibold">
              <i class="bi bi-funnel me-1"></i>
              <?= __('catalog.filters.apply'); ?>
            </button>
            <a href="/categories" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-x-circle me-1"></i>
              <?= __('catalog.filters.reset_all'); ?>
            </a>
          </div>

        </div>
      </form>
    </div>

    <!-- RIGHT: SORT + GRID + PAGINATION -->
    <div class="col-12 col-lg-9">

      <!-- Панель сортування -->
      <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body py-2 px-3 px-md-4">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="small text-muted">
              <?= __('categories.index.summary_prefix'); ?>
              <span class="fw-semibold"><?= count($pageItems); ?></span>
              <?= __('categories.index.summary_of'); ?>
              <span class="fw-semibold"><?= $totalCategories; ?></span>
              <?= __('categories.index.summary_suffix'); ?>
              <?php if ($q !== ''): ?>
                <?= ' ' . __('catalog.search.query_label'); ?>
                «<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>»
              <?php endif; ?>
            </div>

            <div class="d-flex align-items-center gap-2">
              <span class="small text-muted">
                  <?= __('catalog.sort.label'); ?>
              </span>
              <select
                class="form-select form-select-sm"
                name="sort"
                form="filtersForm"
                onchange="document.getElementById('filtersForm').sort.value=this.value;document.getElementById('filtersForm').submit();"
              >
                <option value="name_asc"      <?= $sort === 'name_asc'      ? 'selected' : ''; ?>>
                    <?= __('catalog.sort.name_asc'); ?>
                </option>
                <option value="name_desc"     <?= $sort === 'name_desc'     ? 'selected' : ''; ?>>
                    <?= __('catalog.sort.name_desc'); ?>
                </option>
                <option value="products_desc" <?= $sort === 'products_desc' ? 'selected' : ''; ?>>
                    <?= __('categories.sort.products_desc'); ?>
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- GRID -->
      <?php if (empty($pageItems)): ?>
        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-body text-center py-5">
            <div class="mb-3"><i class="bi bi-folder-x fs-1 text-muted"></i></div>
            <?php if ($q !== ''): ?>
              <h2 class="h5 mb-2">
                  <?= __('categories.empty.filtered.title'); ?>
              </h2>
              <p class="text-muted mb-3">
                  <?= __('categories.empty.filtered.text'); ?>
              </p>
            <?php else: ?>
              <h2 class="h5 mb-2">
                  <?= __('categories.empty.none.title'); ?>
              </h2>
            <?php endif; ?>
            <a href="/categories" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-counterclockwise me-1"></i>
              <?= __('catalog.filters.reset_all'); ?>
            </a>
          </div>
        </div>
      <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 g-md-4">
          <?php foreach ($pageItems as $cat): ?>
            <div class="col">
              <div class="card h-100 border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body d-flex flex-column align-items-start p-3">
                  <div class="d-flex align-items-center justify-content-between w-100 mb-2">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning-subtle"
                         style="width: 40px; height: 40px;">
                      <i class="bi bi-grid-3x3-gap text-warning fs-5"></i>
                    </div>

                    <?php if (!empty($cat['products_count'])): ?>
                      <span class="badge bg-light text-muted small">
                        <?= sprintf(
                            __('categories.card.products_count'),
                            (int)$cat['products_count']
                        ); ?>
                      </span>
                    <?php endif; ?>
                  </div>

                  <h2 class="h6 fw-semibold mb-1">
                    <a href="/categories/subcategory/<?= htmlspecialchars((string)($cat['slug'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                       class="stretched-link text-decoration-none text-dark">
                      <?= htmlspecialchars((string)($cat['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                  </h2>

                  <?php if (!empty($cat['description'])): ?>
                    <p class="text-muted small mb-0">
                        <?= htmlspecialchars((string)$cat['description'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                  <?php else: ?>
                    <p class="text-muted small mb-0">
                        <?= __('categories.card.view_subcategories'); ?>
                    </p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- PAGINATION -->
      <?php if ($totalPages > 1 && !empty($pageItems)): ?>
        <nav aria-label="<?= __('pagination.aria_label'); ?>" class="mt-4">
          <ul class="pagination justify-content-center mb-0">
            <?php if ($page > 1): ?>
              <li class="page-item">
                <a class="page-link"
                   href="<?= htmlspecialchars($buildUrl(['page' => $page - 1]), ENT_QUOTES, 'UTF-8'); ?>"
                   aria-label="<?= __('pagination.prev'); ?>">
                  <span aria-hidden="true">&laquo;</span>
                </a>
              </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item<?= $i === $page ? ' active' : ''; ?>">
                <a class="page-link"
                   href="<?= htmlspecialchars($buildUrl(['page' => $i]), ENT_QUOTES, 'UTF-8'); ?>">
                  <?= $i; ?>
                </a>
              </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
              <li class="page-item">
                <a class="page-link"
                   href="<?= htmlspecialchars($buildUrl(['page' => $page + 1]), ENT_QUOTES, 'UTF-8'); ?>"
                   aria-label="<?= __('pagination.next'); ?>">
                  <span aria-hidden="true">&raquo;</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
      <?php endif; ?>

    </div>
  </div>
</section>
