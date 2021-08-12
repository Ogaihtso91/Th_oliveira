<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;

class UserTypes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if(Auth::check()){
            if($request->user()->user_type != $role){
                return redirect()->route('site.login')->with('error', 'Não autorizado');
            }
            return $next($request);
        } else {
            return redirect()->route('site.login')->with('error','Nao está logado');
        }
    } 
}
