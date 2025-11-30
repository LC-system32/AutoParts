<?php
declare(strict_types=1);

namespace App\Core;

final class Lang
{
    private static string $locale = 'uk';

    /** @var array<string,string> */
    private static array $lines = [];

    public static function init(?string $locale = null): void
    {
        if ($locale === null) {
            $locale = self::$locale;
        }

        $locale = strtolower($locale);
        if (!in_array($locale, ['uk', 'en'], true)) {
            $locale = 'uk';
        }

        self::$locale = $locale;

        // 👇 Дуже важливо: BASE_PATH /App/Lang/{locale}.php
        $file = BASE_PATH . '/App/Lang/' . $locale . '.php';

        if (is_readable($file)) {
            $lines = require $file;
            if (is_array($lines)) {
                self::$lines = $lines;
            } else {
                self::$lines = [];
            }
        } else {
            self::$lines = [];
        }
    }

    public static function getLocale(): string
    {
        return self::$locale;
    }

    public static function get(string $key, ?string $default = null, array $replacements = []): string
    {
        $text = self::$lines[$key] ?? $default ?? $key;

        if ($replacements) {
            foreach ($replacements as $name => $value) {
                $text = str_replace(':' . $name, (string) $value, $text);
            }
        }

        return $text;
    }
}
