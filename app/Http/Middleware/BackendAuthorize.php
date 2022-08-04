<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use Illuminate\Auth\AuthenticationException;

class BackendAuthorize
{
    /**
     * @param  Request  $request
     * @param  $next
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws AuthenticationException
     */
    public function handle($request, $next)
    {
        $user = $request->user();
        if ($user && $user->hasRole('admin')) {
            return $next($request);
        }

        if ($request->wantsJson()) {
            throw new AuthenticationException();
        }

        return redirect(route('home'))->withErrors('You do not have privilege access this page');
    }
}
