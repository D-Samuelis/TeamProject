<?php

namespace App\Application\Auth\Services;

use DomainException;

use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;

use App\Models\Auth\User;
use App\Models\Business\Asset;

class AssetAuthorizationService
{
    public function __construct(
        private AssetRepositoryInterface $assetRepo,
        private UserRepositoryInterface $userRepo,
    ) {}

    public function ensureCanCreateAsset(User $user): void
    {
        if ($user->is_admin) {
            return;
        }
    }

    public function ensureCanDeleteAsset(User $user, Asset $asset): void
    {
        if ($user->is_admin) {
            return;
        }
    }

    public function ensureCanUpdateAsset(User $user, Asset $asset): void
    {
        if ($user->is_admin) {
            return;
        }
    }
}
