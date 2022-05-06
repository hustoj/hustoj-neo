<?php

namespace App\Providers;

use App\Services\AdminChecker;
use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrap();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
        $this->app->singleton(AdminChecker::class, function ($app) {
            return new AdminChecker();
        });
    }

    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            config_path('rabbitmq.php'),
            'rabbitmq'
        );
        $this->mergeConfigFrom(
            config_path('sentry.php'),
            'sentry'
        );
    }
}
