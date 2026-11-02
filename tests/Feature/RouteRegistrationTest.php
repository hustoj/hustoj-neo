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

    public function testAdminGroupUsesPreventRequestForgery()
    {
        $groups = app(\Illuminate\Contracts\Http\Kernel::class)->getMiddlewareGroups();

        $this->assertContains(
            \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
            $groups['admin']
        );
        $this->assertNotContains(
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            $groups['admin']
        );
    }

    public function testGlobalMiddlewareKeepsLaravel13Defaults()
    {
        $middleware = app(\Illuminate\Contracts\Http\Kernel::class)->getGlobalMiddleware();

        $this->assertContains(\Illuminate\Http\Middleware\ValidatePathEncoding::class, $middleware);
        $this->assertContains(\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class, $middleware);
        $this->assertContains(\App\Http\Middleware\TrustProxies::class, $middleware);
    }

    public function testEventListenersAreRegisteredExactlyOnce()
    {
        $rawListeners = app('events')->getRawListeners();

        $this->assertSame(
            [\Illuminate\Auth\Listeners\SendEmailVerificationNotification::class],
            $rawListeners[\Illuminate\Auth\Events\Registered::class] ?? []
        );
        $this->assertSame(
            [\App\Listeners\LoginListener::class.'@handle'],
            $rawListeners[\Illuminate\Auth\Events\Login::class] ?? []
        );
        $this->assertSame(
            [\App\Listeners\LoginListener::class.'@handle'],
            $rawListeners[\Illuminate\Auth\Events\Failed::class] ?? []
        );

        $queryListeners = array_values(array_filter(
            $rawListeners[\Illuminate\Database\Events\QueryExecuted::class] ?? [],
            static fn ($listener) => is_string($listener) && str_starts_with($listener, \App\Listeners\DatabaseListener::class)
        ));
        $this->assertSame([\App\Listeners\DatabaseListener::class.'@handle'], $queryListeners);
    }
}
