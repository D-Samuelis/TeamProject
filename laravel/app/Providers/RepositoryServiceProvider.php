<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Bind interfaces → implementations so DI works: when the app needs a UserRepository, it will get an EloquentUserRepository, etc.
class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \App\Infrastructure\Auth\TokenServiceInterface::class,
            \App\Infrastructure\Auth\SanctumTokenService::class
        );

        $this->app->bind(
            \App\Domain\User\Repositories\UserRepositoryInterface::class,
            \App\Infrastructure\User\Repositories\EloquentUserRepository::class
        );

        $this->app->bind(
            \App\Domain\Business\Repositories\BusinessRepositoryInterface::class,
            \App\Infrastructure\Business\Repositories\EloquentBusinessRepository::class
        );

        $this->app->bind(
            \App\Domain\Business\Repositories\ServiceRepositoryInterface::class,
            \App\Infrastructure\Business\Repositories\EloquentServiceRepository::class
        );

        $this->app->bind(
            \App\Domain\Business\Repositories\BranchRepositoryInterface::class,
            \App\Infrastructure\Business\Repositories\EloquentBranchRepository::class
        );
    }

    public function boot() {}
}
