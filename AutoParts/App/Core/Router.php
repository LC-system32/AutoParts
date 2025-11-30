<?php

declare(strict_types=1);

namespace App\Core;
use App\Controllers\ErrorController;
/**
 * Router
 *
 * Registers HTTP routes and dispatches requests to the appropriate controller
 * method. Route patterns can include named parameters in curly braces, e.g.
 * `/products/{slug}`. Parameters will be passed to the controller method via
 * the Request object's routeParam() accessor.
 */
class Router
{
    /**
     * The request being dispatched
     */
    private Request $request;
    private ErrorController $error; 

    /**
     * Registered routes grouped by HTTP method
     *
     * @var array<string, array<int, array{
     *      pattern:string,
     *      regex:string,
     *      paramNames:array<int,string>,
     *      action:array{0:string,1:string}
     * }>>
     */
    private array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'PATCH'  => [],
        'DELETE' => [],
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Register a GET route
     */
    public function get(string $pattern, array $action): void
    {
        $this->addRoute('GET', $pattern, $action);
    }

    /**
     * Register a POST route
     */
    public function post(string $pattern, array $action): void
    {
        $this->addRoute('POST', $pattern, $action);
    }

    /**
     * Internal helper to add a route
     *
     * @param string $method  HTTP method
     * @param string $pattern Route pattern (e.g. '/products/{slug}')
     * @param array  $action  [ControllerClass, method]
     */
    private function addRoute(string $method, string $pattern, array $action): void
    {
        // Нормалізуємо шлях: без кінцевого слеша (крім '/')
        if ($pattern !== '/' && str_ends_with($pattern, '/')) {
            $pattern = rtrim($pattern, '/');
        }

        $paramNames = [];

        // Замінюємо {name} на regex-частини, паралельно збираючи список параметрів
        $regexBody = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#',
            static function (array $matches) use (&$paramNames): string {
                $name = $matches[1];
                $paramNames[] = $name;
                // іменована група для зручності
                return '(?P<' . $name . '>[^/]+)';
            },
            $pattern
        );

        // Екрануємо роздільник regex-а, на всякий випадок
        $regexBody = str_replace('#', '\#', $regexBody);

        // Підсумковий патерн: від початку до кінця строкі, без жодних ']' в кінці
        $regex = '#^' . $regexBody . '$#u';

        $this->routes[$method][] = [
            'pattern'    => $pattern,
            'regex'      => $regex,
            'paramNames' => $paramNames,
            'action'     => $action,
        ];
    }

    /**
     * Dispatch the current request to the first matching route
     */
    public function dispatch(): void
    {
        $method = $this->request->method();
        $path   = $this->request->path();

        // Нормалізація шляху
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        $routesForMethod = $this->routes[$method] ?? [];

        foreach ($routesForMethod as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                // Встановлюємо параметри роуту в Request
                foreach ($route['paramNames'] as $name) {
                    if (isset($matches[$name])) {
                        $this->request->setRouteParam($name, $matches[$name]);
                    }
                }

                [$controllerClass, $methodName] = $route['action'];

                if (!class_exists($controllerClass)) {
                    http_response_code(500);
                    echo 'Controller not found';
                    return;
                }

                $controller = new $controllerClass($this->request);

                if (!method_exists($controller, $methodName)) {
                    http_response_code(500);
                    echo 'Method not found';
                    return;
                }

                // Виклик методу контролера
                $controller->$methodName();
                return;
            }
        }

        // No matching route
        // Якщо маршрут не знайдено:
        http_response_code(404);
        $controller = new ErrorController($this->request);
        $controller->notFound();
        exit;
    }
}
