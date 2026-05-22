<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Sentry\Laravel\Integration as SentryIntegration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        then: function () {
            Route::middleware('web')
                ->group(function () {
                    Route::namespace('App\Http\Controllers')
                        ->group(base_path('routes/web.php'));
                });

            Route::prefix('admin')
                ->middleware('admin')
                ->group(function () {
                    Route::namespace('App\Http\Controllers')
                        ->group(base_path('routes/admin.php'));
                });

            Route::prefix('judge')
                ->group(function () {
                    Route::namespace('App\Http\Controllers')
                        ->group(base_path('routes/judge.php'));
                });
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 保留 Laravel 13 默认全局栈，只替换 TrustProxies 为自定义 header bitmask 版本
        $middleware->replace(
            \Illuminate\Http\Middleware\TrustProxies::class,
            \App\Http\Middleware\TrustProxies::class,
        );

        // web 组追加业务中间件
        $middleware->web(append: [
            \App\Http\Middleware\SetupConfig::class,
        ]);

        // admin 组(完全自定义,含 session/csrf + 后台鉴权)
        $middleware->group('admin', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\BackendAuthorize::class,
        ]);

        // 路由别名
        $middleware->alias([
            'authorizeContest' => \App\Http\Middleware\AuthorizeContest::class,
            'zip.response' => \App\Http\Middleware\CompressContent::class,
            'cors'         => \App\Http\Middleware\EnableCrossSite::class,
            'guest'        => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        SentryIntegration::handles($exceptions);

        // 未认证用户重定向到 /login（与旧 Handler::unauthenticated 保持一致）
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $exception, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest('login');
        });

        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
            \Illuminate\Session\TokenMismatchException::class,
            \Illuminate\Validation\ValidationException::class,
        ]);

        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);
    })
    ->create();
