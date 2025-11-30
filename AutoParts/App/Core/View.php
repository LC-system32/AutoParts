<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Lang;

class View
{
    public string $layout = 'layouts/main';

    /**
     * @param string               $view
     * @param array<string, mixed> $params
     */
    public function render(string $view, array $params = []): void
    {
        $basePath   = dirname(__DIR__, 2) . '/public/views/';
        $viewFile   = $basePath . $view . '.php';
        $layoutFile = $basePath . $this->layout . '.php';

        if (!is_readable($viewFile)) {
            http_response_code(500);
            echo 'View not found: ' . htmlspecialchars($view);
            return;
        }

        // додаємо локаль в параметри
        $params['locale'] = Lang::getLocale();

        // передаємо змінні у view
        extract($params, EXTR_SKIP);

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        if (is_readable($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content;
        }
    }
}
