<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

/**
 * InfoController
 *
 * Serves static informational pages like FAQ, privacy policy, about us and payment/delivery details.
 * These pages are rendered from simple views located in public/views/info/.
 */
class InfoController extends Controller
{
    /**
     * FAQ page
     */
    public function faq(): void
    {
        $this->render('info/faq', [
            'pageTitle' => 'Часті запитання',
        ]);
    }

    /**
     * Privacy policy page
     */
    public function privacy(): void
    {
        $this->render('info/privacy', [
            'pageTitle' => 'Політика конфіденційності',
        ]);
    }

    /**
     * About us page
     */
    public function about(): void
    {
        $this->render('info/about', [
            'pageTitle' => 'Про нас',
        ]);
    }

    /**
     * Payment & delivery information page
     */
    public function paymentDelivery(): void
    {
        $this->render('info/payment_delivery', [
            'pageTitle' => 'Оплата та доставка',
        ]);
    }

    /**
     * Contact information page
     */
    public function contact(): void
    {
        $this->render('info/contact', [
            'pageTitle' => 'Контакти',
        ]);
    }
}