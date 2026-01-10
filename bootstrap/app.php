<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using: function(){
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path(path: 'routes/api.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/myatmin/api.php'));

            Route::middleware('api')
                ->prefix(prefix: 'api')
                ->group( base_path(path: 'routes/shwekyi/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->group(base_path('routes/myatmin/web.php'));

             Route::middleware('web')
                ->group(base_path(path: 'routes/shwekyi/web.php'));

        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Add middleware for large file uploads
        $middleware->web(append: [
            \App\Http\Middleware\IncreaseUploadLimits::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
