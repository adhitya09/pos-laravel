<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    /**
     * Map common route actions to permission action names.
     *
     * @var array<string,string>
     */
    protected array $actionMap = [
        'index' => 'viewAny',
        'create' => 'create',
        'store' => 'create',
        'show' => 'view',
        'edit' => 'update',
        'update' => 'update',
        'destroy' => 'delete',
        'restore' => 'restore',
        'forceDelete' => 'forceDelete',
        'export' => 'export',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string $permission = null)
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        // If explicit permission passed to middleware, use it.
        if (! $permission) {
            $route = $request->route();
            $routeName = $route ? $route->getName() : null;

            if ($routeName) {
                if (str_contains($routeName, '.')) {
                    [$resource, $action] = explode('.', $routeName, 2);
                } else {
                    $resource = $routeName;
                    $action = 'view';
                }

                $actionSegment = explode('.', $action)[0] ?? $action;
                $permAction = $this->actionMap[$actionSegment] ?? null;

                // Fallback to method-based action when action is unknown
                if (! $permAction) {
                    $method = $request->method();
                    $permAction = match ($method) {
                        'POST' => 'create',
                        'PUT', 'PATCH' => 'update',
                        'DELETE' => 'delete',
                        default => 'viewAny',
                    };
                }

                $permission = $resource . '.' . $permAction;
            }
        }

        // If still not able to determine a permission, allow the route.
        if (! $permission) {
            return $next($request);
        }

        if (method_exists($user, 'hasPermission')) {
            if (! $user->hasPermission($permission)) {
                // For API / AJAX requests return 403 JSON
                if ($request->expectsJson() || $request->wantsJson()) {
                    abort(403);
                }

                // Authenticated but unauthorized users should receive 403
                // to avoid redirect loops (do not redirect to other protected routes).
                abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
            }
        }

        return $next($request);
    }
}
