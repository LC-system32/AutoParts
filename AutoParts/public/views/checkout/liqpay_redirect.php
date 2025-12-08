<?php
/**
 * @var string $data
 * @var string $signature
 * @var float  $totalAmount
 */
?>
<section class="py-4">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center">
                <h1 class="h4 mb-3">Перехід до оплати</h1>
                <p class="mb-2">
                    До сплати:
                    <strong><?= number_format($totalAmount, 2, '.', ' '); ?> UAH</strong>
                </p>
                <p class="text-muted small mb-4">
                    Ви будете перенаправлені на захищену сторінку LiqPay для завершення оплати.
                </p>

                <form method="POST" action="https://www.liqpay.ua/api/3/checkout">
                    <input type="hidden" name="data"
                           value="<?= htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="signature"
                           value="<?= htmlspecialchars($signature, ENT_QUOTES, 'UTF-8'); ?>">

                    <button type="submit" class="btn btn-success px-4">
                        Перейти до оплати
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
