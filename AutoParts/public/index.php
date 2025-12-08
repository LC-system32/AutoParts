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


ini_set('display_errors', '1');
error_reporting(E_ALL);

session_start();

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function (string $class): void {
    
    if (str_starts_with($class, 'App\\')) {
        
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

require BASE_PATH . '/App/helpers.php';

Config::load(BASE_PATH . '/.env');

$availableLocales = ['uk', 'en'];

if (isset($_GET['lang']) && in_array($_GET['lang'], $availableLocales, true)) {
    $_SESSION['lang'] = $_GET['lang'];
}
$currentLocale = $_SESSION['lang'] ?? 'uk';

Lang::init($currentLocale);error_log('LANG: locale=' . $currentLocale . ' count=' . count((new \ReflectionClass(\App\Core\Lang::class))->getStaticProperties()['lines'] ?? []));

$request = new Request();
$router  = new Router($request);

require BASE_PATH . '/config/routes.php';

$router->dispatch();