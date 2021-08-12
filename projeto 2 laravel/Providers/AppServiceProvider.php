<?php

namespace App\Providers;

use App\SocialTecnologyImage;
use App\SocialTecnologyFile;
use App\Observers\SocialTecnologyFileObserver;
use App\Observers\SocialTecnologyImageObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        SocialTecnologyImage::observe(SocialTecnologyImageObserver::class);
        SocialTecnologyFile::observe(SocialTecnologyFileObserver::class);

        /**
         * New tag for Blade templates
         * <code>
         * {? $old_section = "whatever" ?}
         * </code>
         */
        Blade::extend(function($value) {
            return preg_replace('/\{\?(.+)\?\}/', '<?php ${1} ?>', $value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Service provider for admin pages
        if (\Request::is(['admin', 'admin/*'])) {
            view()->share('global_isAdmin', true);
            $this->app->register('App\Providers\AdminServiceProvider');
        } else {
            view()->share('global_isAdmin', false);
        }
    }
}
