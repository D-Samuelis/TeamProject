<?php

namespace App\Providers;

use App\Domain\Business\Entities\Branch;
use App\Domain\Business\Entities\Business;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Policies\BranchPolicy;
use App\Policies\BusinessPolicy;


class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Business::class => BusinessPolicy::class,
        Branch::class => BranchPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        Gate::before(function ($user) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });
    }
}
