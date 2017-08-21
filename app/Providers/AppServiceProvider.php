<?php

namespace App\Providers;

use App\Entities\Option;
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
        $this->initialSettings();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    private function initialSettings()
    {
        $options = Option::all(['key', 'value']);
        $options = $options->keyBy('key')->map(function ($item) {
            return $item->value;
        });
        config(['options' => $options]);
    }
}
