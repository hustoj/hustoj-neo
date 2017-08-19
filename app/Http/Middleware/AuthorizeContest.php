<?php

namespace App\Http\Middleware;

use App\Repositories\ContestRepository;
use Illuminate\Http\Request;

class AuthorizeContest
{
    /**
     * @param Request $request
     * @param         $next
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, $next)
    {
        $route   = app('router')->getRoutes()->match($request);
        $contest = $route->parameter('contest');

        $contest = app(ContestRepository::class)->find($contest);

        if ($contest->private == 0 || can_attend($contest)) {
            return $next($request);
        }

        return redirect(route('contest.index'))->with('error', 'You do not have privilege access contest ' . $contest->id);

    }
}