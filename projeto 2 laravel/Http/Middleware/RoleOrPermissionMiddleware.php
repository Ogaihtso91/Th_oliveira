<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RoleOrPermissionMiddleware
{
    public function handle($request, Closure $next, $roleOrPermission)
    {
        if (!auth('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $rolesOrPermissions = is_array($roleOrPermission)
        ? $roleOrPermission
        : explode('|', $roleOrPermission);

        if ( !(auth('admin')->user()->hasAnyRole($rolesOrPermissions)) && !(auth('admin')->user()->hasAnyPermission($rolesOrPermissions))) {
            throw UnauthorizedException::forRolesOrPermissions($rolesOrPermissions);
        }

        return $next($request);
    }
}

