<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        Relation::morphMap([
            'business' => \App\Models\Business\Business::class,
            'branch'   => \App\Models\Business\Branch::class,
            'service'  => \App\Models\Business\Service::class,
        ]);
    }
}
