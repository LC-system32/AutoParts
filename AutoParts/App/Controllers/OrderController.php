<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\Cart;
use App\Core\Csrf;

/**
 * OrderController
 */
class OrderController extends Controller
{
    /**
     * Display the checkout page
     */
    public function checkout(): void
    {
        try {
            $cart = Cart::getCart();
        } catch (\Throwable $e) {
            $this->flash('error', 'Кошик порожній або недоступний.');
            $this->redirect('/cart');
        }

        if (empty($cart['items'])) {
            $this->flash('error', 'Ваш кошик порожній.');
            $this->redirect('/cart');
        }

        try {
            $deliveryMethods = \App\Models\Method::delivery();
        } catch (\Throwable $e) {
            $deliveryMethods = [];
        }
        try {
            $paymentMethods = \App\Models\Method::payment();
        } catch (\Throwable $e) {
            $paymentMethods = [];
        }

        $coupon = $_SESSION['cart_coupon'] ?? null;

        $this->render('checkout/index', [
            'pageTitle'       => 'Оформлення замовлення',
            'cart'            => $cart,
            'deliveryMethods' => $deliveryMethods,
            'paymentMethods'  => $paymentMethods,
            'coupon'          => $coupon,
        ]);
    }


    /**
     * Submit the checkout form and create an order
     */
    public function submit(): void
    {
        $token = $this->request->post('_csrf');
        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/checkout');
        }
        // Build the payload for creating an order
        $payload = [
            'cart_id'            => Cart::getCartId(),
            'delivery_method_id' => (int)$this->request->post('delivery_method_id'),
            'payment_method_id'  => (int)$this->request->post('payment_method_id'),
            'address'            => trim($this->request->post('address', '')),
            'notes'              => trim($this->request->post('notes', '')),
        ];
        // Remove empty fields
        foreach ($payload as $key => $value) {
            if ($value === '' || $value === null) {
                unset($payload[$key]);
            }
        }
        try {
            $order = Order::createOrder($payload);
            // Clear cart
            unset($_SESSION['cart_id']);
            $this->flash('success', 'Замовлення оформлено успішно.');
            $this->redirect('/');
        } catch (\Throwable $e) {
            $this->flash('error', 'Не вдалося оформити замовлення: ' . $e->getMessage());
            $this->redirect('/checkout');
        }
    }

    /**
     * List orders for current user
     */
    public function orders(): void
    {
        $orders = [];
        try {
            $orders = Order::listForCurrentCustomer();
        } catch (\Throwable $e) {
            $this->flash('error', 'Не вдалося завантажити замовлення.');
        }
        $this->render('orders/index', [
            'pageTitle' => 'Мої замовлення',
            'orders'    => $orders,
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }
}
