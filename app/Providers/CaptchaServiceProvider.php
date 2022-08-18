<?php

namespace App\Providers;

use App\Support\Recaptcha\Verifier;
use Illuminate\Support\ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $app = $this->app;

        $this->bootConfig();

        $app['validator']->extend('captcha', function ($attribute, $value) use ($app) {
            return $app['captcha']->verify($value, $app['request']->getClientIp());
        });
    }

    public function register()
    {
        $this->app->singleton('captcha', function ($app) {
            return new Verifier(
                $app['config']['captcha.secret'],
                $app['config']['captcha.sitekey'],
                $app['config']['captcha.options']
            );
        });
    }

    /**
     * Booting configure.
     */
    protected function bootConfig()
    {
        $path = __DIR__.'/../../config/captcha.php';

        $this->mergeConfigFrom($path, 'captcha');

        if (function_exists('config_path')) {
            $this->publishes([$path => config_path('captcha.php')]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['captcha'];
    }
}
