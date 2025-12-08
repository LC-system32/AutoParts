<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Wishlist;
use App\Core\Csrf;

/**
 * WishlistController
 */
class WishlistController extends Controller
{
    public function add(): void
    {
        $this->requireAuth();
        $token = $this->request->post('_csrf');
        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/profile/wishlist');
        }
        $productId = (int)$this->request->post('product_id');
        try {
            Wishlist::add($productId);
            $this->flash('success', 'Додано до wishlist.');
        } catch (\Throwable $e) {
            $this->flash('error', $e->getMessage());
        }
        $this->redirect('/profile/wishlist');
    }

    public function remove(): void
    {
        $this->requireAuth();
        $token = $this->request->post('_csrf');
        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/profile/wishlist');
        }
        $productId = (int)$this->request->post('product_id');
        try {
            Wishlist::remove($productId);
            $this->flash('success', 'Видалено з wishlist.');
        } catch (\Throwable $e) {
            $this->flash('error', $e->getMessage());
        }
        $this->redirect('/profile/wishlist');
    }
}