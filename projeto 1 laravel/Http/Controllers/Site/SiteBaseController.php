<?php

namespace App\Http\Controllers\Site;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class SiteBaseController extends Controller
{
    public $user;

    // Por enquanto vazio
    public function __construct()
    {
        $route_name = \Illuminate\Support\Facades\Route::currentRouteName();
        View::share('route_name', $route_name);
        $this->user();
    }

    protected function user()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            View::share('user_view', $this->user);
            return $next($request);
        });
    }
}
