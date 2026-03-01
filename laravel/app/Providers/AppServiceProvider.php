<?php

namespace App\Providers;

use App\Http\Middleware\CheckRole;
use Illuminate\Support\ServiceProvider;

use App\Domain\User\Services\PasswordHasher;
use App\Infrastructure\Auth\LaravelPasswordHasher;
use App\Mcp\Servers\WeatherServer;
use App\Mcp\Servers\TaskServer;
use Laravel\Mcp\Facades\Mcp;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PasswordHasher::class,
            LaravelPasswordHasher::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('role', CheckRole::class);

        // Register MCP Servers
        Mcp::local('weather', WeatherServer::class);
        Mcp::local('task', TaskServer::class);
    }
}
