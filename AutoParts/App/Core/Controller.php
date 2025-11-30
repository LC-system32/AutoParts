<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Base Controller
 *
 * All controllers in the application should extend this base class. It
 * provides access to the current Request, a View renderer, helpers for
 * generating responses, redirects and flashing messages, as well as simple
 * authentication checks. If you need more advanced features you can
 * introduce additional traits or helper classes.
 */
abstract class Controller
{
    protected Request $request;
    protected View $view;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->view    = new View();
    }

    /**
     * Render a view with optional parameters
     *
     * @param string               $view   Relative view path (e.g. 'home/index')
     * @param array<string, mixed> $params Data passed to the view
     */
    protected function render(string $view, array $params = []): void
    {
        $this->view->render($view, $params);
    }

    /**
     * Redirect to another URL
     *
     * @param string $url Absolute or relative URL
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Add a flash message to the session
     */
    protected function flash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Retrieve and clear a flash message
     */
    protected function getFlash(string $key): ?string
    {
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    }

    /**
     * Check if the user is authenticated
     */
    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Check if the currently logged in user has a specific role.
     *
     * This helper looks at the `roles` array stored in the session by the
     * authentication logic. Roles are normalized to lowercase strings.
     *
     * @param string $role  The role code to check (e.g. "admin", "manager")
     * @return bool         True if the user has the role, false otherwise
     */
    protected function hasRole(string $role): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }
        $roles = $_SESSION['user']['roles'] ?? [];
        if (!is_array($roles)) {
            return false;
        }
        $role = strtolower($role);
        foreach ($roles as $r) {
            if (strtolower((string)$r) === $role) {
                return true;
            }
        }
        return false;
    }

    /**
     * Require the user to have at least one of the given roles. If the
     * requirement is not met, the user is redirected to the home page with a
     * flash error. Use this in admin-only controllers to protect routes.
     *
     * @param array<string> $roles  List of acceptable roles
     */
    protected function requireRole(array $roles): void
    {
        // Ensure the user is authenticated first
        if (!$this->isAuthenticated()) {
            $this->flash('error', 'Для доступу необхідно увійти.');
            $this->redirect('/login');
        }
        $userRoles = $_SESSION['user']['roles'] ?? [];
        if (!is_array($userRoles)) {
            $userRoles = [];
        }
        // Normalize roles for comparison
        $userRolesLower = array_map(static function ($r) {
            return strtolower((string)$r);
        }, $userRoles);
        $requiredLower = array_map('strtolower', $roles);

        // Check for any intersection
        $allowed = false;
        foreach ($requiredLower as $req) {
            if (in_array($req, $userRolesLower, true)) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            $this->flash('error', 'У вас немає прав для доступу до цієї сторінки.');
            $this->redirect('/');
        }
    }

    /**
     * Require the user to be authenticated; redirects to login if not
     */
    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            $this->flash('error', 'Для доступу необхідно увійти.');
            $this->redirect('/login');
        }
    }
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        // Дуже важливо: завершити виконання, щоб нічого зайвого не дописалось
        exit;
    }

}