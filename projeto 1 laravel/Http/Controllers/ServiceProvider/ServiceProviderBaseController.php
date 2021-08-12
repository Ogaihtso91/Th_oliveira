<?php

namespace App\Http\Controllers\ServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ServiceProviderBaseController extends Controller
{
    public $user;
    public $serviceProvider;
    
    public function __construct()
    {
        $route_name = \Illuminate\Support\Facades\Route::currentRouteName();
        View::share('site_name', 'Au!Pet');
        View::share('page_title', 'Administração');
        View::share('route_name', $route_name);
        $this->user();
    }

    protected function user()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->serviceProvider = $this->user->serviceProvider;
            View::share('user_view', $this->user);
            View::share('serviceProvider', $this->serviceProvider);
            return $next($request);
        });
    }

}
