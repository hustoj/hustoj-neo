<?php

namespace App\Providers;

use App\Entities\Solution;
use App\Entities\User;
use App\Observers\UserDeletedObserver;
use App\Observers\UserSolutionCountObserver;
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
        Solution::observe(UserSolutionCountObserver::class);
        User::deleted(UserDeletedObserver::class);
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
