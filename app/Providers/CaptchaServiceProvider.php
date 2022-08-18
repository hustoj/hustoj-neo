<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(\Anhskohbo\NoCaptcha\NoCaptchaServiceProvider::class);
        class_alias(\Anhskohbo\NoCaptcha\Facades\NoCaptcha::class, 'NoCaptcha');
    }

}
