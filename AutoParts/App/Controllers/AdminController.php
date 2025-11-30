<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ApiClient;
use App\Core\Csrf;
use App\Models\User;

/**
 * AdminController
 *
 * Адмін-панель AutoParts: робота з користувачами, сесіями, замовленнями,
 * відгуками, підтримкою, каталогом та маркетингом.
 */
class AdminController extends Controller
{
    /**
     * Перевірка CSRF-токена для POST-запитів адмінки.
     */
    protected function requireCsrf(): void
    {
        $token = $this->request->post('_csrf') ?? ($_POST['_csrf'] ?? null);

        if (!Csrf::verify($token)) {
            $this->flash('error', 'Сесія форми завершилась. Будь ласка, спробуйте ще раз.');
            $referer = $_SERVER['HTTP_REFERER'] ?? '/';
            $this->redirect($referer ?: '/');
            exit;
        }
    }

    /**
     * Безпечне читання рядкового параметра з query (GET).
     */
    private function queryString(string $name, string $default = ''): string
    {
        $value = $this->request->query($name);

        if ($value === null) {
            return $default;
        }

        if (is_array($value)) {
            $first = reset($value);
            return $first === false ? $default : (string)$first;
        }

        return (string)$value;
    }

    /**
     * Безпечне читання рядкового параметра з post (POST).
     */
    private function postString(string $name, string $default = ''): string
    {
        $value = $this->request->post($name);

        if ($value === null) {
            return $default;
        }

        if (is_array($value)) {
            $first = reset($value);
            return $first === false ? $default : (string)$first;
        }

        return (string)$value;
    }

    // ---------------------------------------------------------------------
    // DASHBOARD
    // ---------------------------------------------------------------------

    /**
     * Головна сторінка адмінки з короткою статистикою.
     *
     * GET /admin
     */
    public function dashboard(): void
    {
        $this->requireRole(['admin', 'manager']);

        $stats          = [];
        $recentOrders   = [];
        $pendingReviews = [];
        $openTickets    = [];

        $periodRaw     = $this->queryString('period', 'today');
        $allowed       = ['today', 'week', 'month', 'all'];
        $currentPeriod = in_array($periodRaw, $allowed, true) ? $periodRaw : 'today';

        // ✅ ЛИШЕ ОДИН ВИКЛИК – /api/admin/stats
        try {
            $stats = ApiClient::get('/api/admin/stats', [
                'period' => $currentPeriod,
            ]);
        } catch (\Throwable $e) {
            error_log('ADMIN DASHBOARD STATS ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося завантажити статистику адмін-панелі.');
        }

        try {
            $recentOrders = ApiClient::get('/api/admin/orders', [
                'limit' => 10,
            ]);
        } catch (\Throwable $e) {
            error_log('ADMIN DASHBOARD ORDERS ERROR: ' . $e->getMessage());
        }

        try {
            // Тягнемо відгуки зі статусом pending через загальний ендпоінт
            $pendingReviews = ApiClient::get('/api/admin/reviews', [
                'status' => 'pending',
            ]);
        } catch (\Throwable $e) {
            error_log('ADMIN DASHBOARD REVIEWS ERROR: ' . $e->getMessage());
        }


        try {
            $openTickets = ApiClient::get('/api/admin/support-tickets', [
                'status' => 'open',
            ]);
        } catch (\Throwable $e) {
            error_log('ADMIN DASHBOARD TICKETS ERROR: ' . $e->getMessage());
        }

        $this->render('admin/dashboard', [
            'pageTitle'      => 'Адмін-панель',
            'stats'          => $stats,
            'recentOrders'   => $recentOrders,
            'pendingReviews' => $pendingReviews,
            'openTickets'    => $openTickets,
            'flash'          => $this->getFlash('error') ?? $this->getFlash('success'),
            'currentPeriod'  => $currentPeriod,
        ]);
    }

    // ---------------------------------------------------------------------
    // USERS / ROLES
    // ---------------------------------------------------------------------

    /**
     * Користувачі та ролі.
     *
     * GET  /admin/users
     * POST /admin/users  – (опційно) оновлення ролей з таблиці
     */
    public function users(): void
    {
        $this->requireRole(['admin', 'manager']);

        if ($this->request->method() === 'POST') {
            $this->requireCsrf();

            $userId = (int)$this->postString('user_id', '0');
            $roles  = $this->request->post('roles') ?? [];

            if (!is_array($roles)) {
                $roles = [];
            }

            try {
                if (method_exists(User::class, 'updateRoles')) {
                    $updated = User::updateRoles($userId, $roles);
                } else {
                    $response = ApiClient::post('/api/admin/users/' . $userId . '/roles', [
                        'roles' => $roles,
                    ]);
                    $updated = !empty($response['success']);
                }

                if ($updated) {
                    $this->flash('success', 'Ролі користувача успішно оновлено.');
                } else {
                    $this->flash('error', 'Не вдалося оновити ролі користувача.');
                }
            } catch (\Throwable $e) {
                error_log('ADMIN USERS ROLES ERROR: ' . $e->getMessage());
                $this->flash('error', 'Сталася помилка під час оновлення ролей.');
            }

            $this->redirect('/admin/users');
            return;
        }

        // фільтри
        $q      = $this->queryString('q');
        $role   = $this->queryString('role');
        $status = $this->queryString('status');

        $filters = [
            'q'      => $q,
            'role'   => $role,
            'status' => $status,
        ];

        $users = [];
        // у методі users() замініть отримання списку користувачів:
        try {
            if (method_exists(User::class, 'allUsers')) {
                $users = User::allUsers($filters);
            } else {
                // 🔧 було: $users = ApiClient::get('/api/admin/users', $filters);
                $resp  = ApiClient::get('/api/admin/users', $filters);
                $users = $resp['data'] ?? $resp ?? [];
            }
        } catch (\Throwable $e) {
            error_log('ADMIN USERS LIST ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося отримати список користувачів.');
        }


        $rolesList = [];
        try {
            $resp      = ApiClient::get('/api/admin/roles');
            $rolesList = $resp['data'] ?? $resp ?? [];
        } catch (\Throwable $e) {
            error_log('ADMIN ROLES LIST ERROR: ' . $e->getMessage());
        }


        $this->render('admin/users', [
            'pageTitle' => 'Користувачі',
            'users'     => $users,
            'roles'     => $rolesList,
            'filters'   => $filters,
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * Форма створення/редагування користувача.
     *
     * GET /admin/users/create
     * GET /admin/users/{id}/edit
     */
    public function usersEdit(): void
    {
        $this->requireRole(['admin', 'manager']);

        $idParam = $this->request->routeParam('id');
        $userId  = $idParam !== null ? (int)$idParam : 0;

        $user = null;
        $userRoleIds = [];

        if ($userId > 0) {
            try {
                $resp = ApiClient::get('/api/admin/users/' . $userId);
                $user = $resp['data'] ?? $resp ?? null;

                // 🔧 нормалізація до асоціативного масиву
                if (is_object($user)) {
                    $user = json_decode(json_encode($user), true);
                }
            } catch (\Throwable $e) {
                error_log('ADMIN USER SHOW ERROR: ' . $e->getMessage());
                $this->flash('error', 'Не вдалося завантажити користувача.');
            }

            if ($user && isset($user['role_ids']) && is_array($user['role_ids'])) {
                $userRoleIds = array_map('intval', $user['role_ids']);
            } elseif ($user && isset($user['roles']) && is_array($user['roles'])) {
                foreach ($user['roles'] as $r) {
                    if (is_array($r) && isset($r['id'])) $userRoleIds[] = (int)$r['id'];
                }
            }
        }

        $rolesList = [];
        try {
            $resp      = ApiClient::get('/api/admin/roles');
            $rolesList = $resp['data'] ?? $resp ?? [];
            if (is_object($rolesList)) $rolesList = json_decode(json_encode($rolesList), true);
        } catch (\Throwable $e) {
            error_log('ADMIN ROLES FOR USER EDIT ERROR: ' . $e->getMessage());
        }

        $this->render('admin/users_edit', [
            'pageTitle'   => $userId > 0 ? 'Редагування користувача' : 'Створення користувача',
            'user'        => $user,
            'roles'       => $rolesList,
            'userRoleIds' => $userRoleIds,
            'userId'      => $userId, // 👈 передаємо окремо
            'flash'       => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }


    /**
     * POST /admin/users/create
     */
    public function usersCreate(): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->requireCsrf();

        $data = [
            'email'            => $this->postString('email'),
            'login'            => $this->postString('login'),
            'password'         => $this->postString('password'),
            'password_confirm' => $this->postString('password_confirm'),
            'first_name'       => $this->postString('first_name'),
            'last_name'        => $this->postString('last_name'),
            'phone'            => $this->postString('phone'),
            'is_active'        => $this->request->post('is_active') ? true : false,
            'roles'            => (array)($this->request->post('roles') ?? []),
        ];

        // Проста локальна валідація
        $errors = [];
        if ($data['email'] === '') {
            $errors['email'] = 'Email є обовʼязковим.';
        }
        if ($data['login'] === '') {
            $errors['login'] = 'Логін є обовʼязковим.';
        }
        if ($data['password'] === '') {
            $errors['password'] = 'Пароль є обовʼязковим.';
        }
        if ($data['password'] !== '' && strlen($data['password']) < 8) {
            $errors['password'] = 'Пароль має містити щонайменше 8 символів.';
        }
        if ($data['password_confirm'] === '' || $data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Паролі не співпадають.';
        }

        if (!empty($errors)) {
            $this->flash('error', 'Виправте помилки у формі.');
            $this->redirect('/admin/users/create');
            return;
        }

        try {
            $response = ApiClient::post('/api/admin/users', $data);

            $ok     = is_array($response) && (!empty($response['success']) || !empty($response['id']));
            $userId = (int)($response['id'] ?? 0);

            if ($ok && $userId > 0) {
                $this->flash('success', 'Користувача створено.');
                $this->redirect('/admin/users/' . $userId . '/edit');
                return;
            }

            // Розбір помилки з API у поля
            $apiErr = is_array($response) ? ($response['error'] ?? $response['message'] ?? $response['raw'] ?? '') : '';
            $apiErrLc = mb_strtolower((string)$apiErr);

            if (strpos($apiErrLc, 'email') !== false) {
                $errors['email'] = 'Некоректний або зайнятий Email.';
            }
            if (strpos($apiErrLc, 'логін') !== false || strpos($apiErrLc, 'login') !== false) {
                $errors['login'] = 'Логін вже використовується або порожній.';
            }
            if (strpos($apiErrLc, 'паролі не співпадають') !== false) {
                $errors['password_confirm'] = 'Паролі не співпадають.';
            }
            if (strpos($apiErrLc, 'мінімум 8') !== false) {
                $errors['password'] = 'Пароль має містити щонайменше 8 символів.';
            }
            if (empty($errors)) {
                // Загальна помилка, якщо не вдалося розпізнати
                $errors['email'] = 'Перевірте заповнення полів.';
            }

            $this->flash('error', $apiErr ?: 'Не вдалося створити користувача.');
            $this->redirect('/admin/users/create');
        } catch (\Throwable $e) {
            error_log('ADMIN USER CREATE ERROR: ' . $e->getMessage());
            $this->flash('error', 'Сталася помилка при створенні користувача: ' . $e->getMessage());
            $this->redirect('/admin/users/create');
        }
    }


    /**
     * POST /admin/users/{id}/update
     */
    public function usersUpdate(): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->requireCsrf();

        $idParam = $this->request->routeParam('id');
        $userId  = $idParam !== null ? (int)$idParam : 0;

        if ($userId <= 0) {
            $this->flash('error', 'Невірний користувач.');
            $this->redirect('/admin/users');
            return;
        }

        $data = [
            'email'      => $this->postString('email'),
            'login'      => $this->postString('login'),
            'first_name' => $this->postString('first_name'),
            'last_name'  => $this->postString('last_name'),
            'phone'      => $this->postString('phone'),
            'is_active'  => $this->request->post('is_active') ? true : false,
        ];

        $password        = $this->postString('password');
        $passwordConfirm = $this->postString('password_confirm');
        if ($password !== '' || $passwordConfirm !== '') {
            $data['password']         = $password;
            $data['password_confirm'] = $passwordConfirm;
        }

        $roles = $this->request->post('roles') ?? [];
        if (!is_array($roles)) {
            $roles = [];
        }
        $data['roles'] = $roles;

        try {
            $response = ApiClient::post('/api/admin/users/' . $userId, $data);
            $ok       = is_array($response) && (!empty($response['success']) || !empty($response['updated']));

            if ($ok) {
                $this->flash('success', 'Дані користувача оновлено.');
            } else {
                $this->flash('error', 'Не вдалося оновити користувача.');
            }
        } catch (\Throwable $e) {
            error_log('ADMIN USER UPDATE ERROR: ' . $e->getMessage());
            $this->flash('error', 'Сталася помилка при оновленні користувача.');
        }

        $this->redirect('/admin/users/' . $userId . '/edit');
    }

    /**
     * GET /admin/users/{id}/sessions
     */
    public function userSessions(): void
    {
        $this->requireRole(['admin', 'manager']);

        $userId = (int)$this->request->routeParam('id');

        $sessions = [];
        try {
            // ✅ 1) завжди пробуємо через API
            $resp = ApiClient::get('/api/admin/users/' . $userId . '/sessions');

            // нормалізація у масив
            if (is_string($resp)) {
                $decoded = json_decode($resp, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $resp = $decoded;
                }
            }
            if (is_object($resp)) {
                $resp = json_decode(json_encode($resp), true);
            }

            $sessions = $resp['data'] ?? $resp ?? [];
            if (isset($sessions['data'])) {
                $sessions = $sessions['data'];
            }
            if (is_object($sessions)) {
                $sessions = json_decode(json_encode($sessions), true);
            }
            if (!is_array($sessions)) {
                $sessions = [];
            }

            // ✅ 2) якщо раптом API нічого не дав, і є локальний метод — fallback
            if (empty($sessions) && method_exists(User::class, 'getSessions')) {
                $sessions = User::getSessions($userId) ?: [];
            }
        } catch (\Throwable $e) {
            error_log('ADMIN USER SESSIONS ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося отримати сесії користувача.');
        }

        $this->render('admin/user_sessions', [
            'pageTitle' => 'Сесії користувача',
            'userId'    => $userId,
            'sessions'  => $sessions,
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }


    /**
     * POST /admin/users/{id}/sessions/terminate
     */
    public function userSessionsTerminate(): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->requireCsrf();

        $userId = (int)$this->request->routeParam('id');

        try {
            if (method_exists(User::class, 'terminateSessions')) {
                $ok = User::terminateSessions($userId);
            } else {
                $resp = ApiClient::post('/api/admin/users/' . $userId . '/sessions/terminate', []);
                // якщо прийшов рядок — розпарсимо
                if (is_string($resp)) {
                    $decoded = json_decode($resp, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $resp = $decoded;
                    }
                }
                if (is_object($resp)) {
                    $resp = json_decode(json_encode($resp), true);
                }
                $ok = !empty($resp['success']);
            }

            $this->flash(
                $ok ? 'success' : 'error',
                $ok
                    ? 'Усі сесії користувача було завершено.'
                    : 'Не вдалося завершити сесії користувача.'
            );
        } catch (\Throwable $e) {
            error_log('ADMIN USER TERMINATE SESSIONS ERROR: ' . $e->getMessage());
            $this->flash('error', 'Сталася помилка при завершенні сесій.');
        }

        $this->redirect('/admin/users/' . $userId . '/sessions');
    }


    // ---------------------------------------------------------------------
    // ORDERS
    // ---------------------------------------------------------------------

    /**
     * GET /admin/orders
     */
    public function orders(): void
    {
        $this->requireRole(['admin', 'manager']);

        $status = $this->queryString('status');
        $q      = $this->queryString('q');
        $dFrom  = $this->queryString('date_from', $this->queryString('from_date'));
        $dTo    = $this->queryString('date_to', $this->queryString('to_date'));

        $filters = [
            'status'    => $status,
            'date_from' => $dFrom,
            'date_to'   => $dTo,
            'q'         => $q,
        ];

        $orders     = [];
        $pagination = [
            'page'       => (int)($this->queryString('page', '1')),
            'perPage'    => 20,
            'total'      => 0,
            'totalPages' => 1,
        ];

        try {
            $apiParams = [
                'status'   => $status,
                'from_date' => $dFrom,
                'to_date'  => $dTo,
                'q'        => $q,
                'page'     => $pagination['page'],
            ];

            $data = ApiClient::get('/api/admin/orders', $apiParams);

            if (isset($data['items']) && is_array($data['items'])) {
                $orders = $data['items'];
                $pagination['page']       = (int)($data['page']       ?? $pagination['page']);
                $pagination['perPage']    = (int)($data['perPage']    ?? $pagination['perPage']);
                $pagination['total']      = (int)($data['total']      ?? $pagination['total']);
                $pagination['totalPages'] = (int)($data['totalPages'] ?? $pagination['totalPages']);
            } elseif (is_array($data)) {
                $orders = $data;
            }
        } catch (\Throwable $e) {
            error_log('ADMIN ORDERS LIST ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося завантажити список замовлень.');
        }

        $this->render('admin/orders', [
            'pageTitle'  => 'Замовлення',
            'orders'     => $orders,
            'filters'    => $filters,
            'pagination' => $pagination,
            'flash'      => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * GET /admin/orders/{id}
     */
    public function orderShow(): void
    {
        $this->requireRole(['admin', 'manager']);

        $id = (int)$this->request->routeParam('id');

        $data = null;
        try {
            $data = ApiClient::get('/api/admin/orders/' . $id);
        } catch (\Throwable $e) {
            error_log('ADMIN ORDER SHOW ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося завантажити замовлення.');
        }

        if ($data === null) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Замовлення не знайдено']);
            return;
        }

        if (isset($data['order'])) {
            $order    = $data['order'];
            $items    = $data['items']          ?? [];
            $history  = $data['status_history'] ?? [];
            $payments = $data['payments']       ?? [];
        } else {
            $order    = $data;
            $items    = $data['items']          ?? [];
            $history  = $data['status_history'] ?? [];
            $payments = $data['payments']       ?? [];
        }

        $this->render('admin/order_show', [
            'pageTitle' => 'Замовлення #' . ($order['order_number'] ?? $id),
            'order'     => $order,
            'items'     => $items,
            'history'   => $history,
            'payments'  => $payments,
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * POST /admin/orders/{id}/status
     */
    /**
     * POST /admin/orders/{id}/status
     */
    public function orderStatus(): void
    {
        $this->requireRole(['admin', 'manager']);
        $this->requireCsrf();

        $id = (int)$this->request->routeParam('id');

        $statusRaw = $this->request->post('status') ?? '';
        $status    = is_array($statusRaw)
            ? (string)(reset($statusRaw) ?: '')
            : (string)$statusRaw;

        $status = trim($status);
        $isPaid = (bool)$this->request->post('is_paid');

        // 🔹 Якщо статус не вибраний – навіть не йдемо в API
        if ($status === '') {
            $this->flash('error', 'Оберіть новий статус замовлення.');
            $this->redirect('/admin/orders/' . $id);
            return;
        }

        try {
            $response = ApiClient::post('/api/admin/orders/' . $id . '/status', [
                'status'  => $status,
                'is_paid' => $isPaid,
            ]);

            // Логуємо, щоб бачити реальну відповідь від API
            error_log('ADMIN ORDER STATUS RESPONSE: ' . json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            // 🔹 Гнучке визначення успіху, незалежно від форми відповіді
            $ok = false;
            if (is_array($response)) {
                if (array_key_exists('success', $response)) {
                    $ok = (bool)$response['success'];
                } elseif (array_key_exists('updated', $response)) {
                    $ok = (bool)$response['updated'];
                } elseif (array_key_exists('data', $response)) {
                    // Багато роутів API повертають { success:true, data:{...} },
                    // але ApiClient може віддати тільки data
                    $ok = true;
                } elseif (!empty($response)) {
                    // Якщо просто повернули дані без обгортки – теж вважаємо успіхом
                    $ok = true;
                }
            }

            if ($ok) {
                $this->flash('success', 'Статус замовлення оновлено.');
            } else {
                $this->flash('error', 'Не вдалося оновити статус замовлення.');
            }
        } catch (\Throwable $e) {
            // Сюди потрапимо тільки якщо ApiClient реально кинув виняток
            error_log('ADMIN ORDER STATUS ERROR: ' . $e->getMessage());
            $this->flash('error', 'Сталася помилка при оновленні статусу замовлення.');
        }

        $this->redirect('/admin/orders/' . $id);
    }

    public function productShow(): void
    {
        $this->requireRole(['admin', 'manager']);

        $id = (int)$this->request->routeParam('id');
        if ($id <= 0) {
            $this->flash('error', 'Невірний ID товару.');
            $this->redirect('/admin/products');
            return;
        }

        $product = [];
        $offers  = [];
        $flash   = $this->getFlash('error') ?? $this->getFlash('success');

        try {
            // ✅ ПРАВИЛЬНИЙ URL до Node: GET /api/admin/products/:id
            $data    = ApiClient::get('/api/admin/products/' . $id);
            // adminModel.getAdminProductDetails повертає { product, offers }
            $product = $data['product'] ?? $data;
            $offers  = $data['offers']  ?? [];
        } catch (\Throwable $e) {
            $this->flash('error', 'Не вдалося завантажити товар: ' . $e->getMessage());
            $this->redirect('/admin/products');
            return;
        }

        $this->render('admin/product_show', [
            'pageTitle' => 'Товар #' . $id,
            'product'   => $product,
            'offers'    => $offers,
            'flash'     => $flash,
        ]);
    }


    /**
     * GET /admin/products/create
     * GET /admin/products/{id}/edit
     */
    /**
     * POST /admin/products/store
     * Створення нового товару
     */
    public function productStore(): void
    {
        $this->requireRole(['admin', 'manager']);

        $token = $this->request->post('_csrf');
        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/admin/products/create');
            return;
        }

        $payload = [
            'name'              => trim((string)$this->request->post('name')),
            'slug'              => trim((string)$this->request->post('slug')),
            'sku'               => trim((string)$this->request->post('sku')),
            'brand_id'          => (int)$this->request->post('brand_id'),
            'category_id'       => (int)$this->request->post('category_id'),
            'short_description' => (string)$this->request->post('short_description'),
            'description'       => (string)$this->request->post('description'),
            'is_active'         => $this->request->post('is_active') ? true : false,
        ];

        try {
            // ✅ створення в Node: POST /api/admin/products
            $resp = ApiClient::post('/api/admin/products', $payload);
            $data = $resp['data'] ?? $resp ?? [];
            $id   = (int)($data['id'] ?? 0);

            $this->flash('success', 'Товар створено.');

            if ($id > 0) {
                $this->redirect('/admin/products/' . $id . '/edit');
            } else {
                $this->redirect('/admin/products');
            }
        } catch (\Throwable $e) {
            $this->flash('error', 'Помилка створення товару: ' . $e->getMessage());
            $this->redirect('/admin/products/create');
        }
    }


    /**
     * POST /admin/products/{id}/update
     * Оновлення товару
     */
    public function productUpdate(): void
    {
        $this->requireRole(['admin', 'manager']);

        $id    = (int)$this->request->routeParam('id');
        $token = $this->request->post('_csrf');

        if ($id <= 0) {
            $this->flash('error', 'Невірний ID товару.');
            $this->redirect('/admin/products');
            return;
        }

        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/admin/products/' . $id . '/edit');
            return;
        }

        $payload = [
            'name'              => trim((string)$this->request->post('name')),
            'slug'              => trim((string)$this->request->post('slug')),
            'sku'               => trim((string)$this->request->post('sku')),
            'brand_id'          => (int)$this->request->post('brand_id'),
            'category_id'       => (int)$this->request->post('category_id'),
            'short_description' => (string)$this->request->post('short_description'),
            'description'       => (string)$this->request->post('description'),
            'is_active'         => $this->request->post('is_active') ? true : false,
        ];

        try {
            // ✅ оновлення в Node: POST /api/admin/products/:id
            $resp = ApiClient::post('/api/admin/products/' . $id, $payload);

            $ok = true;
            if (is_array($resp) && array_key_exists('success', $resp)) {
                $ok = (bool)$resp['success'];
            }

            if ($ok) {
                $this->flash('success', 'Товар оновлено.');
            } else {
                $this->flash('error', 'Не вдалося оновити товар.');
            }
        } catch (\Throwable $e) {
            $this->flash('error', 'Помилка оновлення товару: ' . $e->getMessage());
        }

        $this->redirect('/admin/products/' . $id . '/edit');
    }
    public function productEdit(): void
    {
        $this->requireRole(['admin', 'manager']);

        $idParam   = $this->request->routeParam('id');
        $productId = $idParam !== null ? (int)$idParam : 0;

        $product    = null;
        $brands     = [];
        $categories = [];
        $flash      = $this->getFlash('error') ?? $this->getFlash('success');

        try {
            // 1) бренди
            $brandsResp = ApiClient::get('/api/admin/brands', [
                'with_products' => 0,
            ]);
            $brands = $brandsResp['data'] ?? $brandsResp ?? [];

            // 2) категорії
            $catsResp   = ApiClient::get('/api/admin/categories', []);
            $categories = $catsResp['data'] ?? $catsResp ?? [];

            // 3) якщо редагування – підтягуємо товар
            if ($productId > 0) {
                $data    = ApiClient::get('/api/admin/products/' . $productId);
                $product = $data['product'] ?? $data;
            }
        } catch (\Throwable $e) {
            $this->flash('error', 'Помилка завантаження даних товару: ' . $e->getMessage());
            $this->redirect('/admin/products');
            return;
        }

        $this->render('admin/product_form', [
            'pageTitle'  => $productId > 0 ? 'Редагування товару #' . $productId : 'Створення товару',
            'product'    => $product,
            'brands'     => $brands,
            'categories' => $categories,
            'flash'      => $flash,
        ]);
    }

    // ---------------------------------------------------------------------
    // REVIEWS
    // ---------------------------------------------------------------------

    /**
     * GET /admin/reviews/pending
     */
    public function reviewsPending(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);

        $reviews = [];

        try {
            $resp = \App\Core\ApiClient::get('/api/admin/reviews/pending');

            // Логуємо сирий респонс, щоб точно бачити, що приходить
            error_log('ADMIN reviewsPending API raw response: ' . print_r($resp, true));

            if (is_array($resp)) {
                // Варіант 1: { success: true, data: [...] }
                if (array_key_exists('success', $resp)) {
                    if (!empty($resp['success']) && isset($resp['data']) && is_array($resp['data'])) {
                        $reviews = $resp['data'];
                    } else {
                        $err = isset($resp['error']) ? (string)$resp['error'] : 'Unknown API error';
                        error_log('ADMIN reviewsPending API error: ' . $err);
                    }
                } else {
                    // Варіант 2: ApiClient вже повернув масив відгуків напряму
                    $reviews = $resp;
                }
            }

            // Нормалізуємо ключі comment/body, щоб у вʼю можна було брати і те, і те
            foreach ($reviews as &$r) {
                if (isset($r['body']) && !isset($r['comment'])) {
                    $r['comment'] = $r['body'];
                }
                if (isset($r['comment']) && !isset($r['body'])) {
                    $r['body'] = $r['comment'];
                }
            }
            unset($r);
        } catch (\Throwable $e) {
            error_log('ADMIN REVIEWS PENDING ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося завантажити відгуки.');
        }

        $this->render('admin/reviews_pending', [
            'pageTitle' => 'Відгуки на модерації',
            'reviews'   => $reviews,
            'flash'     => $this->getFlash('success') ?? $this->getFlash('error'),
        ]);
    }

    /**
     * POST /admin/reviews/{id}/moderate
     * decision=approve|reject
     */
    public function reviewModerate(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);
        $this->requireCsrf();

        $id = (int)$this->request->routeParam('id');

        $decisionRaw = $this->request->post('decision') ?? '';
        $decision    = is_array($decisionRaw)
            ? (string)(reset($decisionRaw) ?: '')
            : (string)$decisionRaw;

        $decision = $decision === 'approve' ? 'approve' : 'reject';

        $endpoint = $decision === 'approve'
            ? '/api/admin/reviews/' . $id . '/approve'
            : '/api/admin/reviews/' . $id . '/delete';

        try {
            $response = \App\Core\ApiClient::post($endpoint, []);

            // Лог для дебагу
            error_log('ADMIN reviewModerate API raw response: ' . print_r($response, true));

            $ok = false;
            if (is_array($response)) {
                // Якщо є success == true → ок
                if (!empty($response['success']) && empty($response['error'])) {
                    $ok = true;
                }

                // Якщо повернули updated/deleted → теж ок
                if (!empty($response['updated']) || !empty($response['deleted'])) {
                    $ok = true;
                }

                // Якщо є status = 'ok' / 'success' — теж вважаємо успіхом
                if (!empty($response['status']) && in_array($response['status'], ['ok', 'success'], true)) {
                    $ok = true;
                }
            }

            if ($ok) {
                $msg = $decision === 'approve'
                    ? 'Відгук було схвалено.'
                    : 'Відгук було видалено.';
                $this->flash('success', $msg);
            } else {
                $this->flash('error', 'Не вдалося обробити відгук.');
            }
        } catch (\Throwable $e) {
            error_log('ADMIN REVIEW MODERATE ERROR: ' . $e->getMessage());
        }

        $this->redirect('/admin/reviews/pending');
    }

    public function reviewApprove(): void
    {
        $_POST['decision'] = 'approve';
        $this->reviewModerate();
    }

    public function reviewDelete(): void
    {
        $_POST['decision'] = 'reject';
        $this->reviewModerate();
    }


    // ---------------------------------------------------------------------
    // SUPPORT
    // ---------------------------------------------------------------------

    /**
     * GET /admin/support
     */
    // ---------------------------------------------------------------------
    // SUPPORT
    // ---------------------------------------------------------------------

    /**
     * GET /admin/support
     * Список тікетів + фільтри "на самій сторінці" (як у /brands)
     */
    public function support(): void
    {
        $this->requireRole(['admin', 'manager', 'support']);

        // читаємо GET-параметри, але дефолт – "open", якщо статус не переданий
        $status = array_key_exists('status', $_GET)
            ? $this->queryString('status', '')
            : 'open';

        $q = $this->queryString('q', '');

        $tickets = [];

        try {
            // тягнемо всі тікети з API (без фільтрів) —
            // далі фільтрація буде робитись у вʼюшці, як у прикладі з брендами
            $resp = ApiClient::get('/api/admin/support-tickets', []);

            if (is_array($resp) && isset($resp['data']) && is_array($resp['data'])) {
                $tickets = $resp['data'];
            } elseif (is_array($resp)) {
                $tickets = $resp;
            } else {
                $tickets = [];
            }
        } catch (\Throwable $e) {
            error_log('ADMIN SUPPORT LIST ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося завантажити тікети підтримки.');
        }

        $this->render('admin/support', [
            'pageTitle' => 'Підтримка',
            'tickets'   => $tickets, // СИРИЙ список, фільтруєш у вʼюшці
            'filters'   => [
                'status' => $status,
                'q'      => $q,
            ],
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * GET /admin/support/{id}
     * Деталі одного тікета
     */
    public function supportView(): void
    {
        $this->requireRole(['admin', 'manager', 'support']);

        $id = (int)$this->request->routeParam('id');

        $ticket   = null;
        $messages = [];

        try {
            $resp = ApiClient::get('/api/admin/support-tickets/' . $id);

            // Може бути:
            // 1) { success:true, data:{ ticket:{...}, messages:[...] } }
            // 2) { ticket:{...}, messages:[...] }
            // 3) просто { ...полями тікета... }
            $data = $resp;
            if (is_array($resp) && isset($resp['data']) && is_array($resp['data'])) {
                $data = $resp['data'];
            }

            if (isset($data['ticket'])) {
                $ticket   = $data['ticket'];
                $messages = is_array($data['messages'] ?? null) ? $data['messages'] : [];
            } else {
                // якщо data — це вже сам тікет
                $ticket   = $data;
                $messages = is_array($data['messages'] ?? null) ? $data['messages'] : [];
            }
        } catch (\Throwable $e) {
            error_log('ADMIN SUPPORT VIEW ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося завантажити тікет підтримки.');
        }

        if (!$ticket || !is_array($ticket) || empty($ticket['id'])) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Тікет не знайдено']);
            return;
        }

        $this->render('admin/support_view', [
            'pageTitle' => 'Тікет підтримки #' . (int)$ticket['id'],
            'ticket'    => $ticket,
            'messages'  => $messages,
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * POST /admin/support/{id}/status
     */
    public function supportStatus(): void
    {
        $this->requireRole(['admin', 'manager', 'support']);
        $this->requireCsrf();

        $id = (int)$this->request->routeParam('id');

        $statusRaw = $this->request->post('status') ?? '';
        $status    = is_array($statusRaw)
            ? (string)(reset($statusRaw) ?: '')
            : (string)$statusRaw;
        $status = trim($status);

        if ($id <= 0 || $status === '') {
            $this->flash('error', 'Невірні параметри для зміни статусу.');
            $this->redirect('/admin/support/' . $id);
            return;
        }

        try {
            $response = ApiClient::post(
                '/api/admin/support-tickets/' . $id . '/status',
                ['status' => $status]
            );

            error_log('ADMIN SUPPORT STATUS RESPONSE: ' . json_encode($response, JSON_UNESCAPED_UNICODE));

            $ok = false;
            if (is_array($response)) {
                if (array_key_exists('success', $response)) {
                    $ok = (bool)$response['success'];
                } elseif (!empty($response['data'])) {
                    $ok = true;
                } elseif (!empty($response)) {
                    $ok = true;
                }
            }

            if ($ok) {
                $this->flash('success', 'Статус тікета оновлено.');
            } else {
                $this->flash('error', 'Не вдалося оновити статус тікета.');
            }
        } catch (\Throwable $e) {
            $this->flash('error', 'Сталася помилка при оновленні статусу тікета.');
            error_log('ADMIN SUPPORT STATUS ERROR: ' . $e->getMessage());
        }

        $this->redirect('/admin/support/' . $id);
    }

    /**
     * POST /admin/support/{id}/reply
     */
    public function supportReply(): void
    {
        $this->requireRole(['admin', 'manager', 'support']);
        $this->requireCsrf();

        $id = (int)$this->request->routeParam('id');

        $bodyRaw = $this->request->post('body') ?? '';
        $body    = is_array($bodyRaw)
            ? (string)(reset($bodyRaw) ?: '')
            : (string)$bodyRaw;

        $closeTicket = (bool)$this->request->post('close_ticket');

        if (trim($body) === '') {
            $this->flash('error', 'Текст відповіді не може бути порожнім.');
            $this->redirect('/admin/support/' . $id);
            return;
        }

        try {
            $response = ApiClient::post(
                '/api/admin/support-tickets/' . $id . '/reply',
                [
                    'body'         => $body,
                    'close_ticket' => $closeTicket,
                ]
            );

            // Враховуємо різні форми відповіді:
            // { success:true, data:{...} } або просто { id:..., ... }
            $ok = false;
            if (is_array($response)) {
                if (array_key_exists('success', $response)) {
                    $ok = (bool)$response['success'];
                } elseif (!empty($response['data'])) {
                    $ok = true;
                } elseif (!empty($response['id'])) {
                    $ok = true;
                } elseif (!empty($response)) {
                    $ok = true;
                }
            }

            if ($ok) {
                $this->flash(
                    'success',
                    $closeTicket
                        ? 'Відповідь надіслана, тікет закрито.'
                        : 'Відповідь надіслана.'
                );
            } else {
                $this->flash('error', 'Не вдалося надіслати відповідь.');
            }
        } catch (\Throwable $e) {
            error_log('ADMIN SUPPORT REPLY ERROR: ' . $e->getMessage());
            $this->flash('error', 'Сталася помилка при надсиланні відповіді.');
        }

        $this->redirect('/admin/support/' . $id);
    }
    // ---------------------------------------------------------------------
    // CATALOG & MARKETING (нові в’юшки)
    // ---------------------------------------------------------------------

    /**
     * GET /admin/products
     */
    public function products(): void
    {
        $this->requireRole(['admin', 'manager']);

        $q        = $this->queryString('q');
        $brandId  = $this->queryString('brand_id');
        $catId    = $this->queryString('category_id');
        $status   = $this->queryString('status');   // active / inactive / low_stock etc

        $filters = [
            'q'           => $q,
            'brand_id'    => $brandId,
            'category_id' => $catId,
            'status'      => $status,
        ];

        $products   = [];
        $pagination = [
            'page'       => (int)$this->queryString('page', '1'),
            'perPage'    => 20,
            'total'      => 0,
            'totalPages' => 1,
        ];

        try {
            $apiParams = $filters + ['page' => $pagination['page']];
            $data      = ApiClient::get('/api/admin/products', $apiParams);

            if (isset($data['items']) && is_array($data['items'])) {
                $products               = $data['items'];
                $pagination['page']     = (int)($data['page']       ?? $pagination['page']);
                $pagination['perPage']  = (int)($data['perPage']    ?? $pagination['perPage']);
                $pagination['total']    = (int)($data['total']      ?? $pagination['total']);
                $pagination['totalPages'] = (int)($data['totalPages'] ?? $pagination['totalPages']);
            } elseif (is_array($data)) {
                $products = $data;
            }
        } catch (\Throwable $e) {
            error_log('ADMIN PRODUCTS LIST ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося завантажити список товарів.');
        }

        // довідники для фільтрів
        $brands = [];
        $cats   = [];
        try {
            $brands = ApiClient::get('/api/admin/brands');
        } catch (\Throwable $e) {
            error_log('ADMIN PRODUCTS BRANDS ERROR: ' . $e->getMessage());
        }
        try {
            $cats = ApiClient::get('/api/admin/categories');
        } catch (\Throwable $e) {
            error_log('ADMIN PRODUCTS CATEGORIES ERROR: ' . $e->getMessage());
        }

        $this->render('admin/products', [
            'pageTitle'  => 'Товари',
            'products'   => $products,
            'filters'    => $filters,
            'brands'     => $brands,
            'categories' => $cats,
            'pagination' => $pagination,
            'flash'      => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * GET /admin/products/create
     * GET /admin/products/{id}/edit
     */
    public function productForm(): void
    {
        $this->requireRole(['admin', 'manager']);

        $idParam   = $this->request->routeParam('id');
        $productId = $idParam !== null ? (int)$idParam : 0;

        $flash = $this->getFlash('error') ?? $this->getFlash('success');

        // Довідники
        $brands = $categories = $warehouses = $attributes = $attributeOptionsByAttr = $discounts = [];
        $product = null;
        $offers = [];
        $images = [];
        $oil = [];
        $fitments = [];
        $appliedDiscountIds = [];

        try {
            // Бренди/категорії
            $b = ApiClient::get('/api/admin/brands', ['with_products' => '0']);
            $brands = $b['data'] ?? $b ?? [];

            $c = ApiClient::get('/api/admin/categories');
            $categories = $c['data'] ?? $c ?? [];

            // Склади (для offers)
            $w = ApiClient::get('/api/admin/warehouses');
            $warehouses = $w['data'] ?? $w ?? [];

            // Атрибути і опції (потрібні 2 ендпоінти на Node)
            $a  = ApiClient::get('/api/admin/attributes');
            $attributes = $a['data'] ?? $a ?? [];

            $ao = ApiClient::get('/api/admin/attribute-options');
            // очікуємо структуру типу: { data: { [attribute_id]: [ {id,attribute_id,value}, ... ] } }
            $attributeOptionsByAttr = $ao['data'] ?? $ao ?? [];

            // Знижки (візьмемо побільше)
            $d = ApiClient::get('/api/admin/discounts', ['page' => 1, 'perPage' => 200]);
            $discounts = $d['data']['items'] ?? $d['items'] ?? $d ?? [];
        } catch (\Throwable $e) {
            error_log('ADMIN PRODUCT FORM LOOKUPS ERROR: ' . $e->getMessage());
        }

        // Якщо редагуємо — підтягнути весь комплекс даних
        if ($productId > 0) {
            try {
                $full = ApiClient::get('/api/admin/products/' . $productId . '/full'); // ← новий ендпоінт
                $data = $full['data'] ?? $full ?? [];

                $product            = $data['product'] ?? null;
                $offers             = $data['offers'] ?? [];
                $images             = $data['images'] ?? [];
                $oil                = $data['oil'] ?? [];
                $fitments           = $data['fitments'] ?? [];
                $appliedDiscountIds = $data['discount_ids'] ?? [];

                // зручно мати мапу атрибутів у продукті
                if (!empty($data['attributes'])) {
                    $product['attributes'] = $data['attributes'];
                }
            } catch (\Throwable $e) {
                error_log('ADMIN PRODUCT FULL LOAD ERROR: ' . $e->getMessage());
            }
        }

        $this->render('admin/product_form', [
            'pageTitle'               => $productId > 0 ? 'Редагування товару' : 'Створення товару',
            'product'                 => $product,
            'brands'                  => $brands,
            'categories'              => $categories,
            'warehouses'              => $warehouses,
            'attributes'              => $attributes,
            'attributeOptionsByAttr'  => $attributeOptionsByAttr,
            'discounts'               => $discounts,
            'appliedDiscountIds'      => $appliedDiscountIds,
            'offers'                  => $offers,
            'images'                  => $images,
            'oil'                     => $oil,
            'fitments'                => $fitments,
            'flash'                   => $flash,
        ]);
    }

    /**
     * GET /admin/brands
     */
    public function brands(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);

        $q    = $this->queryString('q');
        $only = $this->queryString('with_products');

        $params = [];
        if ($q !== '') {
            $params['q'] = $q;
        }
        if ($only === '1') {
            $params['with_products'] = 1;
        }

        $brands = [];
        try {
            $brands = ApiClient::get('/api/admin/brands', $params);
        } catch (\Throwable $e) {
            error_log('ADMIN BRANDS LIST ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося завантажити бренди.');
        }

        $this->render('admin/brands', [
            'pageTitle' => 'Бренди',
            'brands'    => $brands,
            'filters'   => [
                'q'             => $q,
                'with_products' => $only,
            ],
            'flash'      => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * GET /admin/categories
     */
    public function categories(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);

        $q = $this->queryString('q');

        $categories = [];
        try {
            $categories = ApiClient::get('/api/admin/categories', [
                'q' => $q,
            ]);
        } catch (\Throwable $e) {
            error_log('ADMIN CATEGORIES LIST ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося завантажити категорії.');
        }

        $this->render('admin/categories', [
            'pageTitle'  => 'Категорії',
            'categories' => $categories,
            'filters'    => ['q' => $q],
            'flash'      => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * GET /admin/discounts
     */
    public function discounts(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);

        // фільтри
        $status = $this->queryString('status'); // active / inactive / (може бути пусто)
        $page   = (int)$this->queryString('page', '1');

        $discounts  = [];
        $pagination = [
            'page'       => $page,
            'perPage'    => 20,
            'total'      => 0,
            'totalPages' => 1,
        ];

        try {
            $apiParams = [
                'status' => $status,
                'page'   => $pagination['page'],
            ];

            // 🔹 ApiClient може ПОВЕРНУТИ або:
            //   { items, page, perPage, total, totalPages }
            // або:
            //   { success: true, data: { items, ... } }
            $resp = ApiClient::get('/api/admin/discounts', $apiParams);

            // Якщо ApiClient вже розпаковує `data`, то тут одразу лежить структура з items
            $data = $resp;

            // Якщо раптом ApiClient повернув повністю JSON з Node ({ success, data }),
            // акуратно розпаковуємо:
            if (isset($resp['data']) && is_array($resp['data'])) {
                $data = $resp['data'];
            }

            if (isset($data['items']) && is_array($data['items'])) {
                $discounts               = $data['items'];
                $pagination['page']      = (int)($data['page']       ?? $pagination['page']);
                $pagination['perPage']   = (int)($data['perPage']    ?? $pagination['perPage']);
                $pagination['total']     = (int)($data['total']      ?? $pagination['total']);
                $pagination['totalPages'] = (int)($data['totalPages'] ?? $pagination['totalPages']);
            } elseif (is_array($data)) {
                // fallback: якщо раптом прилетів просто масив знижок без пагінації
                $discounts = $data;
            }
        } catch (\Throwable $e) {
            error_log('ADMIN DISCOUNTS LIST ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося завантажити знижки та промокоди.');
        }

        $this->render('admin/discounts', [
            'pageTitle'  => 'Знижки та промокоди',
            'discounts'  => $discounts,
            'filters'    => ['status' => $status],
            'pagination' => $pagination,
            'flash'      => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }
    public function discountCreate(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);

        $this->render('admin/discount_create', [
            'pageTitle' => 'Нова знижка',
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * POST /admin/discounts/store
     */
    public function discountStore(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);
        $this->requireCsrf();

        $payload = [
            'name'          => $this->postString('name'),
            'code'          => $this->postString('code'),
            'description'   => $this->postString('description'),
            'discount_type' => $this->postString('discount_type', 'percent'),
            'value'         => (float)$this->postString('value', '0'),
            'min_order_sum' => $this->postString('min_order_sum') !== ''
                ? (float)$this->postString('min_order_sum')
                : null,
            'date_from'     => $this->postString('date_from') ?: null,
            'date_to'       => $this->postString('date_to') ?: null,
        ];

        // читаємо і is_active, і (зворотна сумісність) active
        $isActiveRaw = $this->request->post('is_active');
        if ($isActiveRaw === null) {
            $isActiveRaw = $this->request->post('active');
        }
        $payload['is_active'] = $isActiveRaw ? true : false;

        try {
            $resp = ApiClient::post('/api/admin/discounts', $payload);

            $ok = false;
            $id = 0;

            if (is_array($resp)) {
                if (!empty($resp['success']) && !empty($resp['data']['id'])) {
                    $ok = true;
                    $id = (int)$resp['data']['id'];
                } elseif (!empty($resp['id'])) {
                    $ok = true;
                    $id = (int)$resp['id'];
                } elseif (!empty($resp['success'])) {
                    $ok = true;
                }
            }

            if ($ok) {
                $this->flash('success', 'Знижку створено.');
                if ($id > 0) {
                    $this->redirect('/admin/discounts/' . $id . '/edit');
                    return;
                }
            } else {
                $this->flash('error', 'Не вдалося створити знижку.');
            }
        } catch (\Throwable $e) {
            error_log('ADMIN DISCOUNT STORE ERROR: ' . $e->getMessage());
            $this->flash('error', 'Сталася помилка при створенні знижки.');
        }

        $this->redirect('/admin/discounts');
    }


    /**
     * GET /admin/discounts/{id}/edit
     */
    public function discountEdit(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);

        $idParam   = $this->request->routeParam('id');
        $discountId = $idParam !== null ? (int)$idParam : 0;

        if ($discountId <= 0) {
            $this->flash('error', 'Невірний ID знижки.');
            $this->redirect('/admin/discounts');
            return;
        }

        $discount = null;

        try {
            // очікуємо: { success:true, data:{...} } або просто {...}
            $resp     = ApiClient::get('/api/admin/discounts/' . $discountId);
            $discount = $resp['data'] ?? $resp ?? null;
        } catch (\Throwable $e) {
            error_log('ADMIN DISCOUNT EDIT LOAD ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося завантажити знижку.');
            $this->redirect('/admin/discounts');
            return;
        }

        if (!$discount) {
            $this->flash('error', 'Знижку не знайдено.');
            $this->redirect('/admin/discounts');
            return;
        }

        $this->render('admin/discount_edit', [
            'pageTitle' => 'Редагування знижки',
            'discount'  => $discount,
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * POST /admin/discounts/{id}/update
     */
    public function discountUpdate(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);
        $this->requireCsrf();

        $idParam    = $this->request->routeParam('id');
        $discountId = $idParam !== null ? (int)$idParam : 0;

        if ($discountId <= 0) {
            $this->flash('error', 'Невірний ID знижки.');
            $this->redirect('/admin/discounts');
            return;
        }

        $payload = [
            'name'          => $this->postString('name'),
            'code'          => $this->postString('code'),
            'description'   => $this->postString('description'),
            'discount_type' => $this->postString('discount_type', 'percent'),
            'value'         => (float)$this->postString('value', '0'),
            'min_order_sum' => $this->postString('min_order_sum') !== ''
                ? (float)$this->postString('min_order_sum')
                : null,
            'date_from'     => $this->postString('date_from') ?: null,
            'date_to'       => $this->postString('date_to') ?: null,
        ];

        // читаємо і is_active, і (зворотна сумісність) active
        $isActiveRaw = $this->request->post('is_active');
        if ($isActiveRaw === null) {
            $isActiveRaw = $this->request->post('active');
        }
        $payload['is_active'] = $isActiveRaw ? true : false;

        try {
            $resp = ApiClient::post('/api/admin/discounts/' . $discountId, $payload);

            $ok = false;
            if (is_array($resp)) {
                if (array_key_exists('success', $resp)) {
                    $ok = (bool)$resp['success'];
                } elseif (array_key_exists('updated', $resp)) {
                    $ok = (bool)$resp['updated'];
                } elseif (!empty($resp['data'])) {
                    $ok = true;
                } elseif (!empty($resp)) {
                    $ok = true;
                }
            }

            if ($ok) {
                $this->flash('success', 'Знижку оновлено.');
            } else {
                $this->flash('error', 'Не вдалося оновити знижку.');
            }
        } catch (\Throwable $e) {
            error_log('ADMIN DISCOUNT UPDATE ERROR: ' . $e->getMessage());
            $this->flash('error', 'Сталася помилка при оновленні знижки.');
        }

        $this->redirect('/admin/discounts/' . $discountId . '/edit');
    }


    /**
     * POST /admin/discounts/{id}/delete
     */
    public function discountDelete(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);
        $this->requireCsrf();

        $idParam    = $this->request->routeParam('id');
        $discountId = $idParam !== null ? (int)$idParam : 0;

        if ($discountId <= 0) {
            $this->flash('error', 'Невірний ID знижки.');
            $this->redirect('/admin/discounts');
            return;
        }

        try {
            $resp = ApiClient::post('/api/admin/discounts/' . $discountId . '/delete', []);
            $ok   = is_array($resp) && (!empty($resp['success']) || !empty($resp['deleted']));

            if ($ok) {
                $this->flash('success', 'Знижку видалено.');
            } else {
                $this->flash('error', 'Не вдалося видалити знижку.');
            }
        } catch (\Throwable $e) {
            error_log('ADMIN DISCOUNT DELETE ERROR: ' . $e->getMessage());
            $this->flash('error', 'Сталася помилка при видаленні знижки.');
        }

        $this->redirect('/admin/discounts');
    }


    public function categoryStore(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);
        $this->requireCsrf();

        $name = trim($this->postString('name'));

        if ($name === '') {
            $this->flash('error', 'Назва категорії не може бути порожньою.');
            $this->redirect('/admin/categories');
            return;
        }

        $payload = [
            'name' => $name,
            // slug, parent_id, is_active можна не передавати – API сам розрулить
        ];

        try {
            $resp = ApiClient::post('/api/admin/categories', $payload);

            $ok   = is_array($resp) && (!empty($resp['success']) || !empty($resp['data']));
            $data = $resp['data'] ?? $resp ?? [];
            $id   = (int)($data['id'] ?? 0);

            if ($ok) {
                $this->flash('success', 'Категорію створено.');
                if ($id > 0) {
                    $this->redirect('/admin/categories/' . $id . '/edit');
                    return;
                }
            } else {
                $this->flash('error', 'Не вдалося створити категорію.');
            }
        } catch (\Throwable $e) {
            error_log('ADMIN CATEGORY STORE ERROR: ' . $e->getMessage());
            $this->flash('error', 'Сталася помилка при створенні категорії.');
        }

        $this->redirect('/admin/categories');
    }
    public function categoryEdit(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);

        $id = (int)$this->request->routeParam('id');
        if ($id <= 0) {
            $this->flash('error', 'Невірний ID категорії.');
            $this->redirect('/admin/categories');
            return;
        }

        $category   = null;
        $categories = [];
        $flash      = $this->getFlash('error') ?? $this->getFlash('success');

        try {
            // одна категорія
            $resp     = ApiClient::get('/api/admin/categories/' . $id);
            $category = $resp['data'] ?? $resp ?? null;

            // всі категорії для селекту батьківської
            $listResp   = ApiClient::get('/api/admin/categories', []);
            $categories = $listResp['data'] ?? $listResp ?? [];
        } catch (\Throwable $e) {
            error_log('ADMIN CATEGORY EDIT LOAD ERROR: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося завантажити категорію.');
            $this->redirect('/admin/categories');
            return;
        }

        if (!$category) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Категорію не знайдено']);
            return;
        }

        $this->render('admin/category_edit', [
            'pageTitle'  => 'Редагування категорії #' . $id,
            'category'   => $category,
            'categories' => $categories,
            'flash'      => $flash,
        ]);
    }
    public function categoryUpdate(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);
        $this->requireCsrf();

        $id = (int)$this->request->routeParam('id');
        if ($id <= 0) {
            $this->flash('error', 'Невірний ID категорії.');
            $this->redirect('/admin/categories');
            return;
        }

        $name = trim($this->postString('name'));
        if ($name === '') {
            $this->flash('error', 'Назва категорії не може бути порожньою.');
            $this->redirect('/admin/categories/' . $id . '/edit');
            return;
        }

        $slugRaw = trim($this->postString('slug'));
        $parentRaw = $this->request->post('parent_id');
        $parentId  = ($parentRaw === '' || $parentRaw === null)
            ? null
            : (int)$parentRaw;

        $payload = [
            'name'      => $name,
            'slug'      => $slugRaw !== '' ? $slugRaw : null,
            'parent_id' => $parentId,
            'is_active' => $this->request->post('is_active') ? true : false,
        ];

        try {
            $resp = ApiClient::post('/api/admin/categories/' . $id, $payload);

            $ok = true;
            if (is_array($resp) && array_key_exists('success', $resp)) {
                $ok = (bool)$resp['success'];
            }

            if ($ok) {
                $this->flash('success', 'Категорію оновлено.');
            } else {
                $this->flash('error', 'Не вдалося оновити категорію.');
            }
        } catch (\Throwable $e) {
            error_log('ADMIN CATEGORY UPDATE ERROR: ' . $e->getMessage());
            $this->flash('error', 'Сталася помилка при оновленні категорії.');
        }

        $this->redirect('/admin/categories/' . $id . '/edit');
    }
    public function categoryDelete(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);
        $this->requireCsrf();

        $id = (int)$this->request->routeParam('id');
        if ($id <= 0) {
            $this->flash('error', 'Невірний ID категорії.');
            $this->redirect('/admin/categories');
            return;
        }

        try {
            $resp = ApiClient::post('/api/admin/categories/' . $id . '/delete', []);

            $ok = true;
            if (is_array($resp) && array_key_exists('success', $resp)) {
                $ok = (bool)$resp['success'];
            }

            if ($ok) {
                $this->flash('success', 'Категорію видалено.');
            } else {
                $this->flash('error', 'Не вдалося видалити категорію.');
            }
        } catch (\Throwable $e) {
            error_log('ADMIN CATEGORY DELETE ERROR: ' . $e->getMessage());
            $this->flash('error', 'Сталася помилка при видаленні категорії.');
        }

        $this->redirect('/admin/categories');
    }
    // GET /admin/brands/{id}/edit
    public function brandEdit(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);

        $idParam = $this->request->routeParam('id');
        $id      = $idParam !== null ? (int)$idParam : 0;

        $brand = null;

        if ($id > 0) {
            try {
                $resp  = ApiClient::get('/api/admin/brands/' . $id);
                $brand = $resp['data'] ?? $resp ?? null;
            } catch (\Throwable $e) {
                $this->flash('error', 'Не вдалося завантажити бренд: ' . $e->getMessage());
                $this->redirect('/admin/brands');
                return;
            }

            if (!$brand) {
                $this->flash('error', 'Бренд не знайдено.');
                $this->redirect('/admin/brands');
                return;
            }
        }

        $this->render('admin/brand_edit', [
            'pageTitle' => $id > 0 ? 'Редагування бренду' : 'Створення бренду',
            'brand'     => $brand,
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }
    public function brandUpdate(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);

        $idParam = $this->request->routeParam('id');
        $id      = $idParam !== null ? (int)$idParam : 0;

        if ($id <= 0) {
            $this->flash('error', 'Невірний ID бренду.');
            $this->redirect('/admin/brands');
            return;
        }

        $token = $this->request->post('_csrf') ?? ($_POST['_csrf'] ?? null);
        if (!\App\Core\Csrf::verify($token)) {
            $this->flash('error', 'Сесія форми завершилась. Будь ласка, спробуйте ще раз.');
            $this->redirect('/admin/brands/' . $id . '/edit');
            return;
        }

        $payload = [
            'name'      => trim((string)$this->request->post('name')),
            'slug'      => trim((string)$this->request->post('slug')),
            'is_active' => $this->request->post('is_active') ? true : false,
        ];

        try {
            $resp = ApiClient::post('/api/admin/brands/' . $id, $payload);

            $ok = true;
            if (is_array($resp) && array_key_exists('success', $resp)) {
                $ok = (bool)$resp['success'];
            }

            if ($ok) {
                $this->flash('success', 'Бренд оновлено.');
            } else {
                $this->flash('error', 'Не вдалося оновити бренд.');
            }
        } catch (\Throwable $e) {
            $this->flash('error', 'Помилка оновлення бренду: ' . $e->getMessage());
        }

        $this->redirect('/admin/brands/' . $id . '/edit');
    }


    /**
     * POST /admin/brands/store
     * Створення бренду
     */
    public function brandStore(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);
        $this->requireCsrf();

        $name      = $this->postString('name');
        $slug      = $this->postString('slug');
        $logoUrl   = $this->postString('logo_url');
        $isActive  = $this->request->post('is_active') ? true : false;

        if ($name === '') {
            $this->flash('error', 'Назва бренду є обовʼязковою.');
            $this->redirect('/admin/brands');
            return;
        }

        $payload = [
            'name'      => $name,
            'slug'      => $slug,
            'logo_url'  => $logoUrl,
            'is_active' => $isActive,
        ];

        try {
            // POST /api/admin/brands
            $resp = ApiClient::post('/api/admin/brands', $payload);
            $data = $resp['data'] ?? $resp ?? [];

            $id = (int)($data['id'] ?? 0);

            $this->flash('success', 'Бренд створено.');

            if ($id > 0) {
                $this->redirect('/admin/brands/' . $id . '/edit');
            } else {
                $this->redirect('/admin/brands');
            }
        } catch (\Throwable $e) {
            $this->flash('error', 'Помилка створення бренду: ' . $e->getMessage());
            $this->redirect('/admin/brands');
        }
    }

    /**
     * POST /admin/brands/{id}/delete
     */
    public function brandDelete(): void
    {
        $this->requireRole(['admin', 'manager', 'content_manager']);
        $this->requireCsrf();

        $id = (int)$this->request->routeParam('id');
        if ($id <= 0) {
            $this->flash('error', 'Невірний ID бренду.');
            $this->redirect('/admin/brands');
            return;
        }

        try {
            // POST /api/admin/brands/:id/delete
            $resp = ApiClient::post('/api/admin/brands/' . $id . '/delete', []);

            $ok = true;
            if (is_array($resp) && array_key_exists('success', $resp)) {
                $ok = (bool)$resp['success'];
            }

            if ($ok) {
                $this->flash('success', 'Бренд видалено.');
            } else {
                $this->flash('error', 'Не вдалося видалити бренд.');
            }
        } catch (\Throwable $e) {
            $this->flash('error', 'Помилка видалення бренду: ' . $e->getMessage());
        }

        $this->redirect('/admin/brands');
    }
}
