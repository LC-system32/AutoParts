<?php
declare(strict_types=1);

namespace App\Core;

/**
 * HTTP Request
 *
 * This class wraps PHP's superglobals to provide a cleaner API for
 * accessing request data. It supports retrieving the request method,
 * URI path, query parameters, POST data, JSON bodies, and session/cookie
 * values. It also holds any route parameters captured by the router.
 */
class Request
{
    /**
     * Captured route parameters populated by the router
     *
     * @var array<string, string>
     */
    private array $routeParams = [];

    /**
     * Get the HTTP method (in uppercase)
     */
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Get the URI path without query string (always starting with '/')
     */
    public function path(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $qPos = strpos($uri, '?');
        $path = ($qPos === false) ? $uri : substr($uri, 0, $qPos);
        return $path === '' ? '/' : $path;
    }

    /**
     * Get all query string parameters
     *
     * @return array<string, string>
     */
    public function query(): array
    {
        return $_GET;
    }

    /**
     * Get a specific query string parameter
     */
    public function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get a specific POST parameter
     */
    public function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get the parsed body of a JSON request
     *
     * @return array<string, mixed>
     */
    public function json(): array
    {
        static $json = null;
        if ($json === null) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            $json = is_array($data) ? $data : [];
        }
        return $json;
    }

    /**
     * Get a cookie value
     */
    public function cookie(string $name, $default = null)
    {
        return $_COOKIE[$name] ?? $default;
    }

    /**
     * Access the current session array
     */
    public function session(): array
    {
        return $_SESSION;
    }

    /**
     * Set a route parameter captured by the router
     *
     * @internal Used by Router
     */
    public function setRouteParam(string $name, string $value): void
    {
        $this->routeParams[$name] = $value;
    }

    /**
     * Get a captured route parameter
     */
    public function routeParam(string $name, $default = null)
    {
        return $this->routeParams[$name] ?? $default;
    }
}