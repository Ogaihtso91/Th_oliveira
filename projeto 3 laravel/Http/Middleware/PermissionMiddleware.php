<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Models\Permission;
use App\Enums\Permissoes;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $permissions = is_array($permission) ? $permission : explode('|', $permission);

        foreach($permissions as $item){
            if (auth()->user()->hasPermissionTo((int) $item)) return $next($request);
        }

        abort('401');
    }
}
