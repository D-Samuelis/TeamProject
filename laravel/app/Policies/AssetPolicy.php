<?php

namespace App\Policies;

use App\Application\Auth\Services\AssetAuthorizationService;
use App\Models\Auth\User;
use App\Models\Business\Asset;

class AssetPolicy
{
    public function __construct(
        private AssetAuthorizationService $assetAuthService,
    ) {}

    public function before(User $user, $ability, $model = null): ?bool
    {
        // $model may be null for global abilities
        if ($model instanceof Asset) {
            try {
                $this->assetAuthService->ensureCanUpdateAsset($user, $model);
                return true;
            } catch (\DomainException) {
                return false;
            }
        }

        return null; // fallback to normal policy checks
    }

    public function update(User $user, Asset $asset): bool
    {
        try {
            $this->assetAuthService->ensureCanUpdateAsset($user, $asset);
            return true;
        } catch (\DomainException) {
            return false;
        }
    }

    public function destroy(User $user, Asset $asset): bool
    {
        try {
            $this->assetAuthService->ensureCanDeleteAsset($user, $asset);
            return true;
        } catch (\DomainException) {
            return false;
        }
    }
}
