<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('admin')
                ->name('admin.')
                ->middleware(['web', 'auth', 'role:admin'])
                ->group(base_path('routes/admin.php'));

            Route::prefix('agent')
                ->name('agent.')
                ->middleware(['web', 'auth', 'role:admin,agent'])
                ->group(base_path('routes/agent.php'));

            Route::prefix('vendor')
                ->name('vendor.')
                ->middleware(['web', 'auth', 'role:vendor'])
                ->group(base_path('routes/vendor.php'));

            Route::name('user.')
                ->middleware(['web', 'auth', 'role:user'])
                ->group(base_path('routes/user.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
