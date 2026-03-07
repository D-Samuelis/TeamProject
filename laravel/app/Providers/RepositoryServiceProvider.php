<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Bind interfaces → implementations so DI works: when the app needs a UserRepository, it will get an EloquentUserRepository, etc.
class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \App\Repositories\Auth\TokenServiceInterface::class,
            \App\Repositories\Auth\SanctumTokenService::class
        );

        $this->app->bind(
            \App\Domain\User\Interfaces\UserRepositoryInterface::class,
            \App\Repositories\User\UserRepository::class
        );

        $this->app->bind(
            \App\Domain\Business\Interfaces\BusinessRepositoryInterface::class,
            \App\Repositories\Business\BusinessRepository::class
        );

        $this->app->bind(
            \App\Domain\Branch\Interfaces\BranchRepositoryInterface::class,
            \App\Repositories\Branch\BranchRepository::class
        );

        $this->app->bind(
            \App\Domain\Service\Interfaces\ServiceRepositoryInterface::class,
            \App\Repositories\Service\ServiceRepository::class
        );

        $this->app->bind(
            \App\Domain\Asset\Interfaces\AssetRepositoryInterface::class,
            \App\Repositories\Asset\AssetRepository::class
        );
    }

    public function boot() {}
}
