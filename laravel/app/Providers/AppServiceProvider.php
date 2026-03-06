<?php

namespace App\Providers;


use App\Http\Middleware\CheckRole;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\User\Services\PasswordHasher::class,
            \App\Repositories\Auth\LaravelPasswordHasher::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('role', CheckRole::class);
    }
}
