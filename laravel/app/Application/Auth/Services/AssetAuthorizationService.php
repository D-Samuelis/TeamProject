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
        private UserRepositoryInterface  $userRepo,
    ) {}

    public function ensureCanViewAsset(User $user, Asset $asset): void
    {
        if ($user->isAdmin()) return;

        $role = $this->userRepo->getAssetRole($user, $asset);

        if (! $role) {
            throw new DomainException('You do not have permission to view this asset.');
        }
    }

    public function ensureCanCreateAsset(User $user): void
    {
        if ($user->isAdmin()) return;
    }

    public function ensureCanUpdateAsset(User $user, Asset $asset): void
    {
        if ($user->isAdmin()) return;

        $role = $this->userRepo->getAssetRole($user, $asset);

        if (! in_array($role, ['owner', 'manager'], true)) {
            throw new DomainException('Only owners or managers can update this asset.');
        }
    }

    public function ensureCanDeleteAsset(User $user, Asset $asset): void
    {
        if ($user->isAdmin()) return;

        $role = $this->userRepo->getAssetRole($user, $asset);

        if (! in_array($role, ['owner', 'manager'], true)) {
            throw new DomainException('Only owners can delete this asset.');
        }
    }
}
