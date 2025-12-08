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

        $paymentMethodId = (int)$this->request->post('payment_method_id');
        $payWithLiqpay   = $this->request->post('pay_with_liqpay') === '1';

        try {
            $cart = Cart::getCart();
        } catch (\Throwable $e) {
            $this->flash('error', 'Кошик недоступний.');
            $this->redirect('/cart');
        }

        $cartSubtotal = (float)($cart['total'] ?? 0.0);

        $discount = 0.0;
        if (isset($cart['discount'])) {
            $discount = (float)$cart['discount'];
        } elseif (!empty($cart['coupon']) && is_array($cart['coupon'])) {
            $discount = (float)($cart['coupon']['amount'] ?? 0.0);
        }

        if ($discount < 0) {
            $discount = 0.0;
        }
        if ($discount > $cartSubtotal) {
            $discount = $cartSubtotal;
        }

        $grandTotal = isset($cart['total_with_discount'])
            ? (float)$cart['total_with_discount']
            : max(0.0, $cartSubtotal - $discount);

        
        $payload = [
            'cart_id'            => Cart::getCartId(),
            'delivery_method_id' => (int)$this->request->post('delivery_method_id'),
            'payment_method_id'  => $paymentMethodId,
            'address'            => trim($this->request->post('address', '')),
            'notes'              => trim($this->request->post('notes', '')),
        ];

        foreach ($payload as $key => $value) {
            if ($value === '' || $value === null) {
                unset($payload[$key]);
            }
        }

        try {
            
            $order = Order::createOrder($payload);

            if (!$payWithLiqpay || $paymentMethodId === 1) {
                unset($_SESSION['cart_id']);
                $this->flash('success', 'Замовлення оформлено успішно.');
                $this->redirect('/');
                return;
            }
            
            if ($grandTotal <= 0) {
                $this->flash('error', 'Сума замовлення дорівнює 0. Неможливо відправити на оплату.');
                $this->redirect('/checkout');
                return;
            }
            
            $publicKey  = $_ENV['LIQPAY_PUBLIC_KEY']  ?? 'sandbox_i72720455397';
            $privateKey = $_ENV['LIQPAY_PRIVATE_KEY'] ?? 'sandbox_Lay9lyrgfEqpE2hueI5WUarjRdj9OHNs5McNg7C6';
            
            $liqpayOrderId = 'order_' . $order['id'] . '_' . date('YmdHis');

            $params = [
                'public_key'  => $publicKey,
                'version'     => '3',
                'action'      => 'pay',
                'amount'      => number_format($grandTotal, 2, '.', ''), 
                'currency'    => 'UAH',
                'description' => 'Оплата замовлення #' . $order['id'],
                'order_id'    => $liqpayOrderId,
                'result_url'  => 'http://localhost/checkout/liqpay-result?order_id=' . $order['id'],
                'server_url'  => 'http://localhost/checkout/liqpay-callback',
            ];

            $data      = base64_encode(json_encode($params, JSON_UNESCAPED_UNICODE));
            $signature = base64_encode(sha1($privateKey . $data . $privateKey, true));

            $_SESSION['liqpay_last'] = [
                'order_id'  => (int)$order['id'],
                'amount'    => $grandTotal,
                'currency'  => 'UAH',
                'created_at'=> time(),
            ];
            
            $this->render('checkout/liqpay_redirect', [
                'data'        => $data,
                'signature'   => $signature,
                'totalAmount' => $grandTotal,
            ]);
        } catch (\Throwable $e) {
            $this->flash('error', 'Не вдалося оформити замовлення: ' . $e->getMessage());
            $this->redirect('/checkout');
        }
    }

    public function liqpayResult(): void
    {
        $orderId = (int)$this->request->get('order_id', 0);

        $dataB64 = $this->request->post('data') ?? $this->request->get('data');
        $liqpayResponse = null;
        $status   = null;
        $amount   = null;
        $currency = null;
        $paymentId = null;

        if ($dataB64) {
            $decodedJson = base64_decode($dataB64, true);
            if ($decodedJson !== false) {
                $liqpayResponse = json_decode($decodedJson, true);
            }

            if (is_array($liqpayResponse)) {
                $status    = $liqpayResponse['status']    ?? null;
                $amount    = isset($liqpayResponse['amount']) ? (float)$liqpayResponse['amount'] : null;
                $currency  = $liqpayResponse['currency']  ?? 'UAH';
                $paymentId = $liqpayResponse['payment_id'] ?? ($liqpayResponse['transaction_id'] ?? null);
            }
        }

        if ($status === null) {
            $status = $this->request->post('status') ?? $this->request->get('status');
        }

        $sessionLast = $_SESSION['liqpay_last'] ?? null;
        if ($sessionLast && (int)($sessionLast['order_id'] ?? 0) === $orderId) {
            if ($amount === null) {
                $amount = (float)$sessionLast['amount'];
            }
            if ($currency === null) {
                $currency = (string)($sessionLast['currency'] ?? 'UAH');
            }
        }
        
        $isSuccess = in_array($status, ['success', 'sandbox', 'wait_secure'], true);
        
        if ($status === null && $sessionLast && (int)$sessionLast['order_id'] === $orderId) {
            $isSuccess = true;
            $status = 'sandbox'; 
        }

        if ($isSuccess) {
            unset($_SESSION['cart_id']);
        }
        
        $this->render('checkout/liqpay_result', [
            'pageTitle'      => 'Статус оплати',
            'orderId'        => $orderId,
            'isSuccess'      => $isSuccess,
            'status'         => $status,
            'amount'         => $amount,
            'currency'       => $currency,
            'paymentId'      => $paymentId,
            'liqpayResponse' => $liqpayResponse,
        ]);
    }

    /**
     * POST /checkout/liqpay-callback
     * Серверний callback від LiqPay (опціонально, для більш серйозної інтеграції).
     */
    public function liqpayCallback(): void
    {
        $data      = $_POST['data']      ?? null;
        $signature = $_POST['signature'] ?? null;

        if (!$data || !$signature) {
            http_response_code(400);
            echo 'Missing data or signature';
            return;
        }
        http_response_code(200);
        echo 'OK';
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
