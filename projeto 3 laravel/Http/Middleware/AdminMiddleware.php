<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Enums\Permissoes;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = User::count();
        if (!($user == 1)) {
            if (!Auth::user()->hasPermissionTo(Permissoes::AdministrarPapeisPermissoesUsuarios['id'])) { //If user does //not have this permission
                abort('401');
            }
        }

        return $next($request);
    }
}
