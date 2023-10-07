<?php

namespace App\Http\Middleware;

use Closure;

class SetupConfig
{
    /**
     * @param  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (app('auth')->user()) {
            app()->setLocale(app('auth')->user()->locale);
        }

        return $next($request);
    }
}
