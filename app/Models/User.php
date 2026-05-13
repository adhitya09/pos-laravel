<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * User role relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Determine the permission key for a given route name and HTTP method.
     * Mirrors the logic used in PermissionMiddleware to keep permission keys consistent.
     *
     * @param string $routeName
     * @param string $method
     * @return string|null
     */
    public static function permissionFromRouteName(string $routeName, string $method = 'GET'): ?string
    {
        $actionMap = [
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

        if (! $routeName) return null;

        if (str_contains($routeName, '.')) {
            [$resource, $action] = explode('.', $routeName, 2);
        } else {
            $resource = $routeName;
            $action = 'view';
        }

        $actionSegment = explode('.', $action)[0] ?? $action;
        $permAction = $actionMap[$actionSegment] ?? null;

        if (! $permAction) {
            $method = strtoupper($method);
            $permAction = match ($method) {
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => 'viewAny',
            };
        }

        return $resource . '.' . $permAction;
    }

    /**
     * Return the first route name the user can access based on a simple priority map.
     * Returns null when no accessible route exists.
     *
     * @return string|null
     */
    public function getFirstAccessibleRoute(): ?string
    {
        $priority = [
            'dashboard' => 'dashboard.viewAny',
            'pos.index' => 'pos.viewAny',
            'produk.index' => 'produk.viewAny',
            'inventory.index' => 'inventory.viewAny',
            'transaksi.index' => 'transaksi.viewAny',
            'report.index' => 'report.viewAny',
            'payment-method.index' => 'payment-method.viewAny',
            'cash-flow.index' => 'cash-flow.viewAny',
            'user.index' => 'user.viewAny',
            'kategori.index' => 'kategori.viewAny',
            'setting.index' => 'setting.viewAny',
            'role.index' => 'role.viewAny',
        ];

        foreach ($priority as $route => $perm) {
            if ($this->hasPermission($perm) && \Illuminate\Support\Facades\Route::has($route)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Check whether the user has the given permission string via their role.
     * Supports wildcard '*' and prefix wildcards like 'resource.*'.
     */
    public function hasPermission(string $permission): bool
    {
        $role = $this->role;
        if (!$role) return false;

        $perms = $role->permissions ?? [];
        if (in_array('*', $perms, true)) return true;

        foreach ($perms as $p) {
            if ($p === $permission) return true;
            // support resource.* style
            if (str_ends_with($p, '.*')) {
                $prefix = rtrim($p, '*'); // keeps trailing dot
                if (str_starts_with($permission, rtrim($prefix, '.'))) return true;
            }
        }

        return false;
    }
}
