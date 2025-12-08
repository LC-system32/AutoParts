<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Abstract base class for middleware in the project namespace.
 *
 * Middleware can be used to intercept requests before controllers run. Each
 * middleware receives the current Request and can perform checks (e.g.
 * authentication) and either allow the request to continue or return a
 * response/redirect. To use middleware you would extend this class and
 * implement the `handle()` method. The router should be extended to
 * recognise middleware but for this exercise middleware is called manually
 * from controllers where necessary.
 */
abstract class Middleware
{
    /**
     * Execute middleware logic.
     *
     * @param Request $request The current request
     * @return bool true to continue; false to halt
     */
    abstract public function handle(Request $request): bool;
}
