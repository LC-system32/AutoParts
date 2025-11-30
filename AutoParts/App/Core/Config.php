<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Config
 *
 * Reads environment variables from a .env file and provides simple access
 * throughout the application. This class is intentionally simple; if you need
 * more advanced configuration management you can extend it.
 */
class Config
{
    /**
     * Loaded configuration values
     *
     * @var array<string, mixed>
     */
    private static array $data = [];

    /**
     * Load variables from a .env file
     *
     * @param string $envPath Absolute path to the .env file
     */
    public static function load(string $envPath): void
    {
        if (!is_readable($envPath)) {
            return;
        }
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key   = trim($key);
            $value = trim($value);
            if ($key !== '') {
                self::$data[$key] = $value;
                // Also set in $_ENV for compatibility
                $_ENV[$key] = $value;
            }
        }
    }

    /**
     * Get a configuration value
     *
     * @param string     $key     The configuration key
     * @param mixed|null $default Default value if not set
     *
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return self::$data[$key] ?? $_ENV[$key] ?? $default;
    }
}