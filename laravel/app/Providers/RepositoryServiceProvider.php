<?php
// app/Providers/RepositoryServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Infrastructure\Auth\TokenServiceInterface;
use App\Infrastructure\Auth\SanctumTokenService;

// Bind interfaces → implementations so DI works: when the app needs a UserRepository, it will get an EloquentUserRepository, etc.
class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(TokenServiceInterface::class, SanctumTokenService::class);

        // SpatieRoleAssigner is concrete but you can also bind an interface if you prefer
        $this->app->bind(\App\Infrastructure\Auth\SpatieRoleAssigner::class, \App\Infrastructure\Auth\SpatieRoleAssigner::class);
    }

    public function boot() {}
}
