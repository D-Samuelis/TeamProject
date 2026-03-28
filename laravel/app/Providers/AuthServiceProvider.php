<?php

namespace App\Providers;

use App\Models\Business\Asset;
use App\Policies\AssetPolicy;
use Illuminate\Support\ServiceProvider;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use App\Policies\BranchPolicy;
use App\Policies\BusinessPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Business::class => BusinessPolicy::class,
        Branch::class => BranchPolicy::class,
        Asset::class => AssetPolicy::class,
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
    public function boot() {}
}
