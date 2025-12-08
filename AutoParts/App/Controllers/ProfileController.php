<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\User;
use App\Core\Csrf;

/**
 * ProfileController
 */
class ProfileController extends Controller
{
    /**
     * Головна сторінка профілю /profile
     * Тягнемо дані з сесії + оновлюємо їх свіжими з API (/api/users/:id),
     * в тому числі телефон і адресу з таблиці addresses.
     */
    public function index(): void
    {
        $this->requireAuth();

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            $this->flash('error', 'Користувача не знайдено.');
            $this->redirect('/');
        }

        $sessionUser = $_SESSION['user'] ?? [];

        $profile = $sessionUser;

        try {
            
            $fromApi = User::getById((int)$userId);

            if (is_array($fromApi)) {
                
                $profile = array_merge($profile, $fromApi);

                if (!empty($fromApi['phone'])) {
                    $profile['phone'] = $fromApi['phone'];
                } elseif (!empty($fromApi['account_phone'])) {
                    
                    $profile['phone'] = $fromApi['account_phone'];
                }
                
                if (!empty($fromApi['address'])) {
                    $profile['address'] = $fromApi['address'];
                } else {
                    
                    $addrParts = [];
                    foreach (
                        [
                            'address_country',
                            'address_region',
                            'address_city',
                            'address_street_address',
                            'address_postal_code',
                        ] as $key
                    ) {
                        if (!empty($fromApi[$key])) {
                            $addrParts[] = $fromApi[$key];
                        }
                    }

                    if ($addrParts) {
                        $profile['address'] = implode(', ', $addrParts);
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        $this->render('profile/index', [
            'pageTitle'          => 'Мій профіль',
            'user'               => $profile,
            'flash'              => $this->getFlash('error') ?? $this->getFlash('success'),
            'currentProfilePage' => 'overview',
        ]);
    }

    /**
     * Список замовлень користувача
     */
    public function orders(): void
    {
        $this->requireAuth();

        $orders = [];
        try {
            $orders = Order::listForCurrentCustomer();
        } catch (\Throwable $e) {
        }

        $this->render('profile/orders', [
            'pageTitle'          => 'Мої замовлення',
            'orders'             => $orders,
            'flash'              => $this->getFlash('error') ?? $this->getFlash('success'),
            'currentProfilePage' => 'orders',
        ]);
    }

    /**
     * Wishlist
     */
    public function wishlist(): void
    {
        $this->requireAuth();

        $items = [];
        try {
            $items = Wishlist::getWishlist();
        } catch (\Throwable $e) {
        }

        $this->render('profile/wishlist', [
            'pageTitle'          => 'Мій wishlist',
            'items'              => $items,
            'flash'              => $this->getFlash('error') ?? $this->getFlash('success'),
            'currentProfilePage' => 'wishlist',
        ]);
    }

    /**
     * /profile/edit (якщо будеш використовувати окрему сторінку редагування)
     * Логіка така ж, як в index(): стягуємо профіль + адресу з API.
     */
    public function edit(): void
    {
        $this->requireAuth();

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            $this->flash('error', 'Користувача не знайдено.');
            $this->redirect('/profile');
        }

        $sessionUser = $_SESSION['user'] ?? [];
        $profile     = $sessionUser;

        try {
            $fromApi = User::getById((int)$userId);
            if (is_array($fromApi)) {
                $profile = $this->mergeProfileWithApi($sessionUser, $fromApi);
            }
        } catch (\Throwable $e) {
        }

        if (!$profile) {
            $this->flash('error', 'Не вдалося отримати профіль.');
            $this->redirect('/profile');
        }

        $this->render('profile/index', [ 
            'pageTitle'          => 'Мій профіль',
            'user'               => $profile,
            'flash'              => $this->getFlash('error') ?? $this->getFlash('success'),
            'currentProfilePage' => 'overview',
        ]);
    }

    /**
     * Обробка форми "Зберегти зміни" з /profile
     */
    public function update(): void
    {
        $this->requireAuth();

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            $this->flash('error', 'Користувача не знайдено.');
            $this->redirect('/profile');
        }

        $token = $this->request->post('_csrf') ?? $this->request->post('csrf') ?? null;
        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/profile');
        }

        $name      = trim($this->request->post('name', ''));
        $firstName = trim($this->request->post('first_name', ''));
        $lastName  = trim($this->request->post('last_name', ''));

        if ($name !== '' && $firstName === '' && $lastName === '') {
            $parts     = preg_split('/\s+/', $name);
            $firstName = array_shift($parts) ?? '';
            $lastName  = implode(' ', $parts);
        }

        $email   = trim($this->request->post('email', ''));
        $phone   = trim($this->request->post('phone', ''));
        $address = trim($this->request->post('address', ''));

        $payload = [];

        if ($firstName !== '') {
            $payload['first_name'] = $firstName;
        }
        if ($lastName !== '') {
            $payload['last_name'] = $lastName;
        }
        if ($email !== '') {
            $payload['email'] = $email;
        }
        if ($phone !== '') {
            $payload['phone'] = $phone;
        }
        if ($address !== '') {
            
            $payload['address_line'] = $address;
        }
        if (empty($payload)) {
            $this->flash('info', 'Немає змін для збереження.');
            $this->redirect('/profile');
        }
        try {
            $updated = User::updateProfile((int)$userId, $payload);

            $updatedUser = null;
            if (is_array($updated)) {
                
                if (array_key_exists('user', $updated) || array_key_exists('address', $updated)) {
                    $updatedUser = $updated['user'] ?? $updated;
                } else {
                    
                    $updatedUser = $updated;
                }
            }

            if (is_array($updatedUser)) {
                foreach (['email', 'phone', 'login', 'first_name', 'last_name'] as $key) {
                    if (array_key_exists($key, $updatedUser)) {
                        $_SESSION['user'][$key] = $updatedUser[$key];
                    }
                }

                $fullName = trim(
                    ($_SESSION['user']['first_name'] ?? '') . ' ' .
                        ($_SESSION['user']['last_name'] ?? '')
                );

                if ($fullName !== '') {
                    $_SESSION['user']['name'] = $fullName;
                } else {
                    $_SESSION['user']['name'] =
                        $_SESSION['user']['login']
                        ?? $_SESSION['user']['name']
                        ?? 'Користувач';
                }
            }
            if ($address !== '') {
                $_SESSION['user']['address'] = $address;
            }

            $this->flash('success', 'Профіль оновлено.');
            $this->redirect('/profile');
        } catch (\Throwable $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/profile');
        }
    }

    private function mergeProfileWithApi(array $sessionUser, array $fromApi): array
    {
        $user    = $sessionUser;
        $address = null;
        
        if (array_key_exists('user', $fromApi) || array_key_exists('address', $fromApi)) {
            if (!empty($fromApi['user']) && is_array($fromApi['user'])) {
                $user = array_merge($user, $fromApi['user']);
            }
            if (!empty($fromApi['address']) && is_array($fromApi['address'])) {
                $address = $fromApi['address'];
            }
        } else {
            
            $user = array_merge($user, $fromApi);

            
            if (!isset($user['phone']) && isset($fromApi['account_phone'])) {
                $user['phone'] = $fromApi['account_phone'];
            }
            
            $addr = [];
            $map = [
                'address_full_name'      => 'full_name',
                'address_phone'          => 'phone',
                'address_country'        => 'country',
                'address_region'         => 'region',
                'address_city'           => 'city',
                'address_postal_code'    => 'postal_code',
                'address_street_address' => 'street_address',
                'address_comment'        => 'comment',
            ];

            foreach ($map as $src => $dst) {
                if (!empty($fromApi[$src])) {
                    $addr[$dst] = $fromApi[$src];
                }
            }

            if (!empty($addr)) {
                $address = $addr;
            }
        }
        
        if ($address !== null) {
            $user['address'] = $address;
        }

        return $user;
    }
}
