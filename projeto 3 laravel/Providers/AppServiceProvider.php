<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // entrando na atualização do laravel 5.5 pro 5.6
        // inicialmente deixando comentado por ser opcional
        // Blade::withoutDoubleEncoding();
        // Paginator::useBootstrapThree();
    }
}
