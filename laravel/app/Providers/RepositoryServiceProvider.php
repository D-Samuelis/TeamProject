<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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

        $this->app->bind(
            \App\Domain\Rule\Interfaces\RuleRepositoryInterface::class,
            \App\Repositories\Rule\RuleRepository::class
        );
        $this->app->bind(
            \App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface::class,
            \App\Repositories\Appointment\AppointmentRepository::class
        );
    }

    public function boot() {}
}
