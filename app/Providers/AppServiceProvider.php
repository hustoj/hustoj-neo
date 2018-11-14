<?php

namespace App\Providers;

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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
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
