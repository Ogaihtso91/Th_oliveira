<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!auth('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $roles = is_array($role)
            ? $role
            : explode('|', $role);

            if ( auth('admin')->user()->hasAnyRole($roles)) {
                //dd($role);
            /*if (url tem admin) {
                return redirect()->route('admin.login');
            } else {
                return redirect()->route('front.home');
            }*/
        } else {
            return redirect()->route('front.home');
        }

        return $next($request);
    }
}