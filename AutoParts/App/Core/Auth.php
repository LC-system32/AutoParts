<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple Auth helper located in the project namespace.
 *
 * This class centralizes authentication‑related helper methods. It stores the
 * currently logged‑in user in the `$_SESSION['user']` array. In a more
 * advanced implementation you would persist sessions in the database via
 * `user_sessions` and support a long‑lived remember‑me token. Here we
 * implement only the basic runtime helpers required for controllers and
 * views.
 */
class Auth
{
    /**
     * Get current user array or null if not authenticated.
     */
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Check if user is authenticated.
     */
    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Log in a user by storing minimal information in the session.
     *
     * @param int $userId ID of the user in the database
     * @param bool $remember Whether to persist session longer term (not yet implemented)
     */
    public static function login(int $userId, bool $remember = false): void
    {
        $_SESSION['user'] = ['id' => $userId, 'roles' => []];
    }

    /**
     * Log out current user.
     */
    public static function logout(): void
    {
        unset($_SESSION['user']);
    }

    /**
     * Check if current user has a particular role.
     */
    public static function hasRole(string $role): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }
        return in_array($role, $user['roles'] ?? [], true);
    }
}
