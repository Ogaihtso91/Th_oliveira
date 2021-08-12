<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class UserBaseController extends Controller
{
    public $user;
    public $route_name;
    
    public function __construct()
    {
        $route_name = $this->route_name = \Illuminate\Support\Facades\Route::currentRouteName();
        View::share('site_name', 'Au!Pet');
        View::share('page_title', 'Dono');
        View::share('route_name', $route_name);
        $this->user();
    }

    protected function setMeta($name, $value)
    {
        View::share($name, $value);
    }

    protected function user()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            View::share('user_view', $this->user);
            View::share('pets_sidebar', $this->user->pets->take(3));
            View::share('location_sidebar', $this->user->locations->take(5));
            return $next($request);
        });
    }
}
