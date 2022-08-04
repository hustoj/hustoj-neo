<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EnableCrossSite
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if ($response instanceof BinaryFileResponse) {
            return $response;
        }

        $response->header('Access-Control-Allow-Origin', config('app.url'));
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept, X-REQUESTED-WITH, X-XSRF-TOKEN');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
        $response->header('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
