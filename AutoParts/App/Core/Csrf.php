<?php

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function csrfInput(): string
    {
        $token = self::token();

        return '<input type="hidden" name="_csrf" value="' .
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8') .
            '">';
    }

    /**
     * Перевірка токена
     */
    public static function verify(?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $sessionToken = $_SESSION[self::SESSION_KEY] ?? null;

        if (!$token || !$sessionToken) {
            error_log("CSRF VERIFY FAIL: empty token or session");
            return false;
        }


        if (strpos($token, '<input') !== false) {
            if (preg_match('/value="([^"]+)"/', $token, $m)) {
                $token = $m[1];
            }
        }

        $ok = hash_equals($sessionToken, $token);

        error_log("CSRF VERIFY: POST='{$token}' SESSION='{$sessionToken}' RESULT=" . ($ok ? 'OK' : 'FAIL'));

        return $ok;
    }
}
