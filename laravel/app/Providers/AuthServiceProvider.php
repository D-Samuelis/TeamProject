<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Branch;
use App\Models\Business;
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
