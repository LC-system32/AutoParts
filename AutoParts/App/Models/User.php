<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\ApiClient;

/**
 * User Model
 */
class User extends Model
{
    public static function register(array $data): array
    {
        return self::create('/api/auth/register', $data);
    }

    public static function login(array $credentials): array
    {
        return self::create('/api/auth/login', $credentials);
    }

    /**
     * @param int $id
     * @return array<string,mixed>
     */
    public static function getById(int $id): array
    {
        if (!empty($_SESSION['user']) && (int)($_SESSION['user']['id'] ?? 0) === $id) {
            return $_SESSION['user'];
        }
        return self::get('/api/users/' . $id);
    }

    /**
     * @param int $id
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public static function updateProfile(int $id, array $data): array
    {
        return self::update('/api/users/' . $id, $data);
    }
    /**
     * @return array<int,array<string,mixed>>
     */
    public static function allUsers(): array
    {
        return ApiClient::get('/api/admin/users');
    }
    /**
     * @param int $userId
     * @return array<int,array<string,mixed>>
     */
    public static function getSessions(int $userId): array
    {
        $response = ApiClient::get('/api/admin/users/' . $userId . '/sessions');

        return $response['data'] ?? [];
    }
    /**
     * @param int $userId
     * @return bool
     */
    public static function terminateSessions(int $userId): bool
    {
        $response = ApiClient::delete('/api/admin/users/' . $userId . '/sessions');

        return !empty($response['success']);
    }
    /**
     * @param int $userId
     * @param array<int,string> $roles
     * @return array<string,mixed>|null
     */
    public static function updateRoles(int $userId, array $roles): ?array
    {
        $response = ApiClient::post('/api/admin/users/' . $userId . '/roles', [
            'roles' => array_values($roles),
        ]);

        return $response['data'] ?? null;
    }
    /**
     * @param string $email
     * @return array<string,mixed>|null
     */
public static function findByEmail(string $email): ?array
    {
        $email = trim($email);
        if ($email === '') {
            return null;
        }
        $response = ApiClient::get('/api/users/by-email?email=' . urlencode($email));
        $user = $response['data'] ?? $response ?? null;

        if (!$user || empty($user['id'])) {
            return null;
        }

        return [
            'id'         => (int)$user['id'],
            'email'      => $user['email']      ?? '',
            'login'      => $user['login']      ?? '',
            'first_name' => $user['first_name'] ?? '',
            'last_name'  => $user['last_name']  ?? '',
            'phone'      => $user['phone']      ?? '',
            'roles'      => is_array($user['roles'] ?? null) ? $user['roles'] : [],
        ];
    }

    public static function loginOrRegisterViaGoogle(array $data): array
    {
        $email = $data['email'] ?? '';

        if ($email === '') {
            throw new \InvalidArgumentException('Email є обовʼязковим для Google користувача.');
        }

        $payload = [
            'email'      => $email,
            'first_name' => $data['first_name'] ?? '',
            'last_name'  => $data['last_name']  ?? '',
        ];

        
        return ApiClient::post('/api/auth/google', $payload);
    }

    public static function createFromGoogle(array $data): array
    {
        $email     = trim((string)($data['email']      ?? ''));
        $firstName = trim((string)($data['first_name'] ?? ''));
        $lastName  = trim((string)($data['last_name']  ?? ''));

        if ($email === '') {
            throw new \InvalidArgumentException('Email є обовʼязковим для Google користувача.');
        }
        $response = ApiClient::post('/api/auth/google', [
            'email'      => $email,
            'first_name' => $firstName,
            'last_name'  => $lastName,
        ]);

        $user  = $response['user']  ?? $response['data'] ?? null;
        $token = $response['token'] ?? null;

        if (!$user || empty($user['id'])) {
            throw new \RuntimeException('Не вдалося створити/отримати користувача через Google API.');
        }
    
        $result = [
            'id'         => (int)$user['id'],
            'email'      => $user['email']      ?? $email,
            'login'      => $user['login']      ?? ($user['email'] ?? $email),
            'first_name' => $user['first_name'] ?? $firstName,
            'last_name'  => $user['last_name']  ?? $lastName,
            'phone'      => $user['phone']      ?? '',
            'roles'      => is_array($user['roles'] ?? null) ? $user['roles'] : [],
        ];

        if ($token) {
            $result['api_token'] = $token;
        }

        return $result;
    }
}
