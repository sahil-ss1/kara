<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ///use Bootstrap style pagination
        Paginator::useBootstrap();
        if($this->app->environment('staging')) {
            \URL::forceScheme('https');
        }
        Model::preventLazyLoading(! app()->isProduction());
    }
}
