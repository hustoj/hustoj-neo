<?php

namespace Tests\Feature;

use Tests\TestCase;

class RouteRegistrationTest extends TestCase
{
    public function testRouteCacheBuildsSuccessfully()
    {
        $this->artisan('route:cache')->assertSuccessful();
        $this->artisan('route:clear')->assertSuccessful();
    }

    public function testJudgeDataRouteUsesZipResponseMiddleware()
    {
        $route = app('router')->getRoutes()->getByAction('App\Http\Controllers\Judge\ApiController@data');

        $this->assertNotNull($route);
        $this->assertContains('zip.response', $route->gatherMiddleware());
    }

    public function testAdminRoutesUseAdminMiddlewareGroup()
    {
        $route = app('router')->getRoutes()->getByName('admin.home');

        $this->assertNotNull($route);
        $this->assertContains('admin', $route->gatherMiddleware());
    }

    public function testGlobalMiddlewareKeepsLaravel13Defaults()
    {
        $middleware = app(\Illuminate\Contracts\Http\Kernel::class)->getGlobalMiddleware();

        $this->assertContains(\Illuminate\Http\Middleware\ValidatePathEncoding::class, $middleware);
        $this->assertContains(\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class, $middleware);
        $this->assertContains(\App\Http\Middleware\TrustProxies::class, $middleware);
    }
}
