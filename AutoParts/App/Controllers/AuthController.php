<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\ApiClient;
use App\Core\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * GET /login – показати форму входу
     */
    public function login(): void
    {
        // Якщо вже залогінений — відразу в профіль
        if ($this->isAuthenticated()) {
            $this->redirect('/profile');
        }

        $this->render('auth/login', [
            'pageTitle' => 'Вхід',
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * POST /login – обробка логіну через Node API
     */
    public function loginPost(): void
    {
        $csrfToken = $this->request->post('_csrf');
        if (!Csrf::verify($csrfToken)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/login');
        }

        $email    = trim($this->request->post('email', ''));
        $password = (string) $this->request->post('password', '');

        if ($email === '' || $password === '') {
            $this->flash('error', 'Будь ласка, заповніть email та пароль.');
            $this->redirect('/login');
        }

        try {
            $data = ApiClient::post('/api/auth/login', [
                'email'    => $email,
                'password' => $password,
            ]);

            $token = $data['token'] ?? null;
            $user  = $data['user'] ?? $data;

            if (!$user || empty($user['id'])) {
                throw new \RuntimeException('Некоректна відповідь API під час логіну.');
            }

            // Уся робота з сесією – в одному місці
            $this->completeLogin(
                $user,
                $token,
                'Ви успішно увійшли в акаунт.',
                '/profile'
            );
        } catch (\Throwable $e) {
            // error_log('Login error: ' . $e->getMessage());
            $this->flash('error', 'Невірний email або пароль.');
            $this->redirect('/login');
        }
    }

    /**
     * GET /register – форма реєстрації
     */
    public function register(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/profile');
        }

        $this->render('auth/register', [
            'pageTitle' => 'Реєстрація',
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * POST /register – обробка реєстрації
     */
    public function registerPost(): void
    {
        $csrfToken = $this->request->post('_csrf');
        if (!Csrf::verify($csrfToken)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/register');
        }

        $name     = trim($this->request->post('name', ''));
        $email    = trim($this->request->post('email', ''));
        $password = trim($this->request->post('password', ''));
        $confirm  = trim($this->request->post('password_confirm', ''));

        if ($name === '' || $email === '' || $password === '' || $confirm === '') {
            $this->flash('error', 'Будь ласка, заповніть усі поля.');
            $this->redirect('/register');
        }

        if ($password !== $confirm) {
            $this->flash('error', 'Паролі не співпадають.');
            $this->redirect('/register');
        }

        try {
            $data = User::register([
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
            ]);

            $userFromApi = $data['user'] ?? $data;

            $token = $data['token']
                ?? ($data['accessToken'] ?? ($userFromApi['token'] ?? null));

            // Розділяємо name на first_name / last_name, якщо API їх не повернуло
            $firstName = $userFromApi['first_name'] ?? '';
            $lastName  = $userFromApi['last_name']  ?? '';

            if ($firstName === '' && $lastName === '' && $name !== '') {
                $parts     = preg_split('/\s+/', $name);
                $firstName = array_shift($parts) ?? '';
                $lastName  = implode(' ', $parts);
            }

            $userFromApi['first_name'] = $firstName;
            $userFromApi['last_name']  = $lastName;

            $this->completeLogin(
                $userFromApi,
                $token,
                'Реєстрація успішна!',
                '/profile'
            );
        } catch (\Throwable $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/register');
        }
    }

    /**
     * GET /logout – вихід
     */
    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        unset($_SESSION['user'], $_SESSION['api_token']);
        session_regenerate_id(true);
        $this->flash('success', 'Ви вийшли з системи.');
        $this->redirect('/');
    }

    /**
     * GET /auth/google – редірект на Google OAuth
     */
    public function redirectToGoogle(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $config = require BASE_PATH . '/config/google_oauth.php';

        $clientId    = $config['client_id'];
        $redirectUri = $config['redirect_uri'];
        $scopes      = $config['scopes'];

        // Мова з GET
        $lang = $_GET['lang'] ?? 'uk';

        // Куди редіректити після логіну
        $_SESSION['after_login_redirect'] = '/?lang=' . $lang;

        // CSRF state – беремо чистий токен
        $state = Csrf::token();
        $_SESSION['google_oauth_state'] = $state;

        $params = [
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => implode(' ', $scopes),
            'access_type'   => 'offline',
            'prompt'        => 'consent',
            'state'         => $state,
        ];

        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

        header('Location: ' . $authUrl);
        exit;
    }

    /**
     * GET /auth/google/callback – обробка колбеку від Google
     */
    public function handleGoogleCallback(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $config = require BASE_PATH . '/config/google_oauth.php';

        // 1. Перевірка помилок
        if (!empty($_GET['error'])) {
            $this->flash('error', 'Авторизацію Google скасовано.');
            $this->redirect('/login');
        }

        // 2. Перевірка state (CSRF)
        $state = $_GET['state'] ?? null;
        if (
            !$state ||
            !isset($_SESSION['google_oauth_state']) ||
            $state !== $_SESSION['google_oauth_state']
        ) {
            unset($_SESSION['google_oauth_state']);
            $this->flash('error', 'Некоректний Google state. Спробуйте ще раз.');
            $this->redirect('/login');
        }
        unset($_SESSION['google_oauth_state']);

        // 3. Отримуємо code
        $code = $_GET['code'] ?? null;
        if (!$code) {
            $this->flash('error', 'Від Google не прийшов код авторизації.');
            $this->redirect('/login');
        }

        // 4. Обмін коду на токени
        try {
            $tokenResponse = $this->fetchGoogleTokens($config, $code);
        } catch (\Throwable $e) {
            error_log('Google OAuth token failed: ' . $e->getMessage());
            $this->flash('error', 'Помилка Google OAuth: ' . $e->getMessage());
            $this->redirect('/login');
        }

        if (empty($tokenResponse['access_token'])) {
            $this->flash('error', 'Google не повернув access_token.');
            $this->redirect('/login');
        }

        $accessToken = $tokenResponse['access_token'];

        // 5. Отримуємо дані юзера
        try {
            $userInfo = $this->fetchGoogleUserInfo($accessToken);
        } catch (\Throwable $e) {
            error_log('Google userinfo error: ' . $e->getMessage());
            $this->flash('error', 'Не вдалося отримати дані користувача від Google.');
            $this->redirect('/login');
        }

        if (!$userInfo || empty($userInfo['email'])) {
            $this->flash('error', 'Не вдалося отримати email від Google.');
            $this->redirect('/login');
        }

        $email      = $userInfo['email'];
        $givenName  = $userInfo['given_name'] ?? '';
        $familyName = $userInfo['family_name'] ?? '';
        $fullName   = trim(($givenName . ' ' . $familyName)) ?: ($userInfo['name'] ?? '');

        // 6. Логін/реєстрація через Node API (через модель User)
        $result = $this->findOrCreateUserFromGoogle($email, $givenName, $familyName);

        $token = $result['token'] ?? null;
        $user  = $result['user']  ?? null;

        if (!$user || empty($user['id'])) {
            $this->flash('error', 'Не вдалося створити користувача через Google.');
            $this->redirect('/login');
        }

        // 7. Куди редіректити після логіну
        $redirectUrl = $_SESSION['after_login_redirect'] ?? '/';
        unset($_SESSION['after_login_redirect']);

        // 8. Однакова логіка зберігання сесії
        $this->completeLogin(
            $user,
            $token,
            'Ви успішно увійшли через Google.',
            $redirectUrl
        );
    }

    /**
     * Обмін коду авторизації на токени Google
     */
    private function fetchGoogleTokens(array $config, string $code): ?array
    {
        $postData = [
            'code'          => $code,
            'client_id'     => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'redirect_uri'  => $config['redirect_uri'],
            'grant_type'    => 'authorization_code',
        ];

        $ch = curl_init('https://oauth2.googleapis.com/token');

        $caPath = BASE_PATH . '/config/cacert.pem';

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CAINFO         => $caPath,
            CURLOPT_CAPATH         => dirname($caPath),
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('Google token CURL error: ' . $err);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return null;
        }

        /** @var array<string,mixed> $data */
        $data = json_decode($response, true);
        return $data ?: null;
    }

    /**
     * Отримання інформації про користувача з Google
     */
    private function fetchGoogleUserInfo(string $accessToken): ?array
    {
        $ch = curl_init('https://openidconnect.googleapis.com/v1/userinfo');

        $caPath = BASE_PATH . '/config/cacert.pem';

        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CAINFO         => $caPath,
            CURLOPT_CAPATH         => dirname($caPath),
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('Google userinfo CURL error: ' . $err);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return null;
        }

        /** @var array<string,mixed> $data */
        $data = json_decode($response, true);
        return $data ?: null;
    }

    /**
     * Виклик Node API для логіну/реєстрації через Google.
     *
     * Очікується, що User::loginOrRegisterViaGoogle повертає:
     *  - або ['token' => '...', 'user' => [...]]
     *  - або просто масив користувача (тоді token буде null)
     *
     * @return array{token:?string,user:array<string,mixed>}
     */
    private function findOrCreateUserFromGoogle(string $email, string $firstName = '', string $lastName = ''): array
    {
        $data = User::loginOrRegisterViaGoogle([
            'email'      => $email,
            'first_name' => $firstName,
            'last_name'  => $lastName,
        ]);

        $token = $data['token'] ?? null;
        $user  = $data['user']  ?? $data;

        if (!$user || empty($user['id'])) {
            throw new \RuntimeException('Некоректна відповідь API під час Google-логіну.');
        }

        return [
            'token' => $token,
            'user'  => $user,
        ];
    }

    /**
     * Нормалізація даних користувача, оновлення сесії та редірект.
     *
     * @param array<string,mixed> $user
     */
    private function completeLogin(
        array $user,
        ?string $token,
        string $successMessage,
        string $redirectUrl
    ): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        session_regenerate_id(true);

        if ($token) {
            $_SESSION['api_token'] = $token;
        } else {
            unset($_SESSION['api_token']);
        }

        $normalized = [
            'id'         => (int)($user['id'] ?? 0),
            'email'      => $user['email']      ?? '',
            'login'      => $user['login']      ?? ($user['email'] ?? ''),
            'first_name' => $user['first_name'] ?? '',
            'last_name'  => $user['last_name']  ?? '',
            'phone'      => $user['phone']      ?? '',
            'address'    => $user['address']    ?? '',
            'roles'      => is_array($user['roles'] ?? null) ? $user['roles'] : [],
        ];

        $fullName = trim($normalized['first_name'] . ' ' . $normalized['last_name']);
        $normalized['name'] = $fullName !== ''
            ? $fullName
            : ($normalized['login'] ?: ($normalized['email'] ?: 'Користувач'));

        $_SESSION['user'] = $normalized;

        $this->flash('success', $successMessage);
        $this->redirect($redirectUrl);
    }
}
