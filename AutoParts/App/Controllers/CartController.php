<?php
// file: app/Controllers/CartController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Core\Csrf;

final class CartController extends Controller
{
    public function index(): void
    {
        try {
            $cart = Cart::getCart();
        } catch (\Throwable $e) {
            $cart = ['items' => [], 'total' => 0];
        }

        $coupon = $_SESSION['cart_coupon'] ?? null;

        $this->render('cart/index', [
            'pageTitle' => 'Кошик',
            'cart'      => $cart,
            'coupon'    => $coupon,
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    public function add(): void
    {
        $token = $this->request->post('_csrf');
        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/cart');
        }

        $productId = (int)$this->request->post('product_id');
        $qty       = max(1, (int)$this->request->post('quantity', 1));

        try {
            Cart::addItem($productId, $qty);
            $this->flash('success', 'Товар додано до кошика.');
        } catch (\Throwable $e) {
            $this->flash('error', 'Не вдалося додати товар: ' . $e->getMessage());
        }
        $this->redirect('/cart');
    }

    public function update(): void
    {
        $token = $this->request->post('_csrf');
        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/cart');
        }

        $itemId = (int)$this->request->post('item_id');
        $qty    = max(1, (int)$this->request->post('quantity', 1));

        try {
            Cart::updateItem($itemId, $qty);
            $this->flash('success', 'Кількість оновлено.');
        } catch (\Throwable $e) {
            $this->flash('error', 'Не вдалося оновити: ' . $e->getMessage());
        }
        $this->redirect('/cart');
    }

    public function remove(): void
    {
        $token = $this->request->post('_csrf');
        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/cart');
        }

        $itemId = (int)$this->request->post('item_id');

        try {
            Cart::removeItem($itemId);
            $this->flash('success', 'Товар видалено з кошика.');
        } catch (\Throwable $e) {
            $this->flash('error', 'Не вдалося видалити товар: ' . $e->getMessage());
        }
        $this->redirect('/cart');
    }

    public function couponApply(): void
    {
        $token = $this->request->post('_csrf');
        if (!\App\Core\Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/cart');
        }

        $code = strtoupper(trim((string)$this->request->post('code', '')));
        if ($code === '') {
            $this->flash('error', 'Введіть код купона.');
            $this->redirect('/cart');
        }

        try {
            $cart = \App\Models\Cart::getCart();
            $cartId = (int)($cart['id'] ?? 0);

            if ($cartId <= 0) {
                $this->flash('error', 'Кошик не знайдено.');
                $this->redirect('/cart');
            }

            // ✅ Застосовуємо купон (але вже БЕЗ зміни цін у cart_items)
            $resp = \App\Core\ApiClient::post("/api/carts/{$cartId}/discounts/apply", [
                'code' => $code,
            ]);

            $data   = $resp['data'] ?? $resp;
            $coupon = $data['coupon'] ?? null;

            if ($coupon) {
                $_SESSION['cart_coupon'] = [
                    'code'               => (string)($coupon['code'] ?? $code),
                    'name'               => (string)($coupon['name'] ?? ''),
                    'type'               => (string)($coupon['discount_type'] ?? ''),
                    'value'              => (float)($coupon['value'] ?? 0),
                    'amount'             => (float)($coupon['amount'] ?? 0),
                    'discount'           => (float)($data['discount'] ?? 0),
                    'total_with_discount' => (float)($data['total_with_discount'] ?? ($cart['total'] ?? 0)),
                ];

                $this->flash('success', 'Купон застосовано.');
            } else {
                $msg = (string)($resp['error'] ?? 'Купон недійсний або не відповідає умовам.');
                $this->flash('error', $msg);
            }
        } catch (\Throwable $e) {
            error_log('couponApply error: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося застосувати купон.');
        }

        $this->redirect('/cart');
    }


    /** Скасувати купон */
    public function couponRemove(): void
    {
        $token = $this->request->post('_csrf');
        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/cart');
        }
        unset($_SESSION['cart_coupon']);
        $this->flash('success', 'Купон скасовано.');
        $this->redirect('/cart');
    }
}
