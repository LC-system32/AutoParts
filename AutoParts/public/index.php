<?php

declare(strict_types=1);

/**
 * AutoParts Front Controller
 *
 * This file acts as the single entry point into the PHP application. It
 * initializes the environment, loads configuration and routes, and then
 * dispatches the current request through the router to a controller. It
 * implements a very lightweight autoloader that maps the \App namespace
 * onto the App directory. You can swap this out for Composer's autoloader
 * should you decide to adopt it later. Sessions are started here for
 * authentication and CSRF protection.
 */

// Enable error reporting in development
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Start the session
session_start();

// Define the base path of the project
define('BASE_PATH', dirname(__DIR__));

// Simple PSR‑4 compliant autoloader for the App namespace
spl_autoload_register(function (string $class): void {
    // Only autoload classes within the App namespace
    if (str_starts_with($class, 'App\\')) {
        // Convert namespace separators to directory separators
        $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 4));
        $file = BASE_PATH . '/App/' . $relativePath . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

use App\Core\Config;
use App\Core\Router;
use App\Core\Request;
use App\Core\Lang;

// Load global helpers (__, etc.)
require BASE_PATH . '/App/helpers.php';

Config::load(BASE_PATH . '/.env');

// ---------- МОВА ----------
$availableLocales = ['uk', 'en'];

if (isset($_GET['lang']) && in_array($_GET['lang'], $availableLocales, true)) {
    $_SESSION['lang'] = $_GET['lang'];
}

$currentLocale = $_SESSION['lang'] ?? 'uk';

Lang::init($currentLocale);error_log('LANG: locale=' . $currentLocale . ' count=' . count((new \ReflectionClass(\App\Core\Lang::class))->getStaticProperties()['lines'] ?? []));

// --------------------------
// Create the request and router instances
$request = new Request();
$router  = new Router($request);

// Load all route definitions
require BASE_PATH . '/config/routes.php';

// Dispatch the current request
$router->dispatch();
