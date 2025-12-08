<?php
/** @var array<string,mixed> $cart */
/** @var array<string,mixed>|null $coupon */
/** @var string|null $flash */

$subtotal = (float)($cart['total'] ?? 0);
$discount = (float)($coupon['amount'] ?? 0.0);
if ($discount > $subtotal) { $discount = $subtotal; }
$grandTotal = max(0.0, $subtotal - $discount);
?>
<section class="py-4">
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
    <div class="mb-3 mb-md-0">
      <h1 class="h3 fw-bold mb-1 d-flex align-items-center">
        <i class="bi bi-cart3 text-warning fs-3 me-2"></i>
        <?= __('cart.title'); ?>
      </h1>
      <p class="text-muted small mb-0">
        <?= __('cart.subtitle'); ?>
      </p>
    </div>
    <?php if (!empty($cart['items'])): ?>
      <span class="badge rounded-pill text-bg-light py-2 px-3">
        <?= __('cart.badge.items'); ?>
        <strong><?= count($cart['items']); ?></strong>
      </span>
    <?php endif; ?>
  </div>

  <?php if (!empty($flash)): ?>
    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
      <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
      <button type="button"
              class="btn-close"
              data-bs-dismiss="alert"
              aria-label="<?= __('common.close'); ?>"></button>
    </div>
  <?php endif; ?>

  <?php if (empty($cart['items'])): ?>
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-center">
      <div class="mb-3">
        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
             style="width:72px;height:72px;">
          <i class="bi bi-cart-x fs-1 text-muted"></i>
        </div>
      </div>
      <h2 class="h4 fw-semibold mb-2">
        <?= __('cart.empty.title'); ?>
      </h2>
      <p class="text-muted mb-4">
        <?= __('cart.empty.text'); ?>
      </p>
      <a href="/products" class="btn btn-warning text-dark fw-semibold px-4">
        <i class="bi bi-search me-1"></i>
        <?= __('cart.empty.button'); ?>
      </a>
    </div>
  <?php else: ?>

  <div class="row g-4">
    <!-- Ліва колонка: товари -->
    <div class="col-12 col-lg-8">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-header bg-white border-0 py-3">
          <div class="d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><?= __('cart.items.title'); ?></span>
            <span class="small text-muted"><?= __('cart.items.prices_hint'); ?></span>
          </div>
        </div>

        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
              <tr class="small text-muted text-uppercase">
                <th scope="col"><?= __('cart.table.header.product'); ?></th>
                <th scope="col" class="text-end"><?= __('cart.table.header.price'); ?></th>
                <th scope="col" class="text-center"><?= __('cart.table.header.qty'); ?></th>
                <th scope="col" class="text-end"><?= __('cart.table.header.sum'); ?></th>
                <th scope="col" class="text-end"></th>
              </tr>
              </thead>
              <tbody class="fs-6">
              <?php foreach ($cart['items'] as $item): ?>
                <?php
                $product = $item['product'] ?? [];
                $name  = htmlspecialchars($product['name'] ?? __('cart.item.default_name'), ENT_QUOTES, 'UTF-8');
                $slug  = htmlspecialchars($product['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                $price = (float)($item['price'] ?? 0);
                $qty   = (int)($item['quantity'] ?? 1);
                $sum   = $price * $qty;
                $img   = $product['image'] ?? null;
                $imgSafe = $img ? htmlspecialchars($img, ENT_QUOTES, 'UTF-8') : '';
                ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <?php if ($imgSafe): ?>
                        <a href="/products/<?= $slug; ?>" class="me-3 d-none d-sm-block">
                          <img src="<?= $imgSafe; ?>"
                               alt="<?= $name; ?>"
                               class="rounded border"
                               style="width:56px;height:56px;object-fit:contain;">
                        </a>
                      <?php endif; ?>
                      <div>
                        <a href="/products/<?= $slug; ?>"
                           class="text-decoration-none text-dark fw-semibold">
                          <?= $name; ?>
                        </a>
                      </div>
                    </div>
                  </td>
                  <td class="text-end">
                    <span class="fw-semibold">
                      <?= number_format($price, 2, '.', ' '); ?> <?= __('common.currency.uah_short'); ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <form action="/cart/update"
                          method="post"
                          class="cart-qty-form d-inline-flex align-items-center justify-content-center">
                      <?= \App\Core\Csrf::csrfInput(); ?>
                      <input type="hidden" name="item_id" value="<?= (int)$item['id']; ?>">
                      <div class="input-group input-group-sm" style="width:120px;">
                        <button class="btn btn-outline-secondary"
                                type="button"
                                data-cart-qty-btn="dec"
                                title="<?= __('cart.qty.decrease.tooltip'); ?>">
                          <i class="bi bi-dash"></i>
                        </button>
                        <input type="number"
                               name="quantity"
                               value="<?= $qty; ?>"
                               min="1"
                               class="form-control text-center">
                        <button class="btn btn-outline-secondary"
                                type="button"
                                data-cart-qty-btn="inc"
                                title="<?= __('cart.qty.increase.tooltip'); ?>">
                          <i class="bi bi-plus"></i>
                        </button>
                      </div>
                    </form>
                  </td>
                  <td class="text-end">
                    <span class="fw-semibold">
                      <?= number_format($sum, 2, '.', ' '); ?> <?= __('common.currency.uah_short'); ?>
                    </span>
                  </td>
                  <td class="text-end">
                    <form action="/cart/remove"
                          method="post"
                          onsubmit="return confirm(<?= json_encode(__('cart.remove.confirm')); ?>);">
                      <?= \App\Core\Csrf::csrfInput(); ?>
                      <input type="hidden" name="item_id" value="<?= (int)$item['id']; ?>">
                      <button type="submit"
                              class="btn btn-sm btn-outline-danger"
                              title="<?= __('cart.remove.button'); ?>">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card-footer bg-white border-0 py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
          <a href="/products" class="btn btn-link text-decoration-none p-0 mb-2 mb-md-0">
            <i class="bi bi-arrow-left me-1"></i>
            <?= __('cart.continue'); ?>
          </a>
          <form action="/cart/clear"
                method="post"
                onsubmit="return confirm(<?= json_encode(__('cart.clear.confirm')); ?>);">
            <?= \App\Core\Csrf::csrfInput(); ?>
            <button type="submit" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-trash3 me-1"></i>
              <?= __('cart.clear.button'); ?>
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Права колонка: підсумок + купон -->
    <div class="col-12 col-lg-4">
      <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-header bg-white border-0 py-3">
          <span class="fw-semibold"><?= __('coupon.title'); ?></span>
        </div>
        <div class="card-body">
          <?php if ($coupon): ?>
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div>
                <div class="fw-semibold">
                  <?= htmlspecialchars($coupon['code'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <?php if (!empty($coupon['name'])): ?>
                  <div class="small text-muted">
                    <?= htmlspecialchars($coupon['name'], ENT_QUOTES, 'UTF-8'); ?>
                  </div>
                <?php endif; ?>
              </div>
              <span class="badge text-bg-success">
                -<?= number_format($discount, 2, '.', ' '); ?> <?= __('common.currency.uah_short'); ?>
              </span>
            </div>
            <form action="/cart/coupon/remove" method="post" class="mt-2">
              <?= \App\Core\Csrf::csrfInput(); ?>
              <button type="submit" class="btn btn-outline-danger w-100">
                <i class="bi bi-x-circle me-1"></i>
                <?= __('coupon.remove'); ?>
              </button>
            </form>
          <?php else: ?>
            <form action="/cart/coupon/apply" method="post" class="d-flex gap-2">
              <?= \App\Core\Csrf::csrfInput(); ?>
              <input type="text"
                     name="code"
                     class="form-control"
                     placeholder="<?= __('coupon.placeholder'); ?>"
                     required>
              <button type="submit" class="btn btn-warning text-dark fw-semibold">
                <?= __('coupon.apply'); ?>
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
          <span class="fw-semibold"><?= __('cart.summary.title'); ?></span>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted"><?= __('cart.summary.items'); ?></span>
            <span><?= count($cart['items']); ?></span>
          </div>

          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted"><?= __('cart.summary.subtotal'); ?></span>
            <span><?= number_format($subtotal, 2, '.', ' '); ?> <?= __('common.currency.uah_short'); ?></span>
          </div>

          <?php if ($discount > 0): ?>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted"><?= __('cart.summary.coupon'); ?></span>
              <span class="text-success">
                -<?= number_format($discount, 2, '.', ' '); ?> <?= __('common.currency.uah_short'); ?>
              </span>
            </div>
          <?php endif; ?>

          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted"><?= __('cart.summary.delivery'); ?></span>
            <span class="text-success"><?= __('cart.summary.delivery.free'); ?></span>
          </div>

          <hr>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fw-bold"><?= __('cart.summary.total'); ?></span>
            <span class="fw-bold fs-5">
              <?= number_format($grandTotal, 2, '.', ' '); ?> <?= __('common.currency.uah_short'); ?>
            </span>
          </div>

          <a href="/checkout" class="btn btn-warning w-100 text-dark fw-semibold mb-2">
            <i class="bi bi-cash-stack me-1"></i>
            <?= __('cart.summary.checkout'); ?>
          </a>
          <p class="small text-muted mb-0">
            <?= __('cart.summary.delivery_note'); ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.cart-qty-form').forEach(function (form) {
    const input = form.querySelector('input[name="quantity"]');
    if (!input) return;
    form.querySelectorAll('[data-cart-qty-btn]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        let current = parseInt(input.value, 10) || 1;
        if (btn.getAttribute('data-cart-qty-btn') === 'dec') {
          if (current > 1) current -= 1;
        } else if (btn.getAttribute('data-cart-qty-btn') === 'inc') {
          current += 1;
        }
        input.value = current;
        form.submit();
      });
    });
  });
});
</script>
