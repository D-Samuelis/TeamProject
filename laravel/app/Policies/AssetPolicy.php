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

    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function view(User $user, Asset $asset): bool
    {
        return $this->runCheck(fn() => $this->assetAuthService->ensureCanViewAsset($user, $asset));
    }

    public function update(User $user, Asset $asset): bool
    {
        return $this->runCheck(fn() => $this->assetAuthService->ensureCanUpdateAsset($user, $asset));
    }

    public function destroy(User $user, Asset $asset): bool
    {
        return $this->runCheck(fn() => $this->assetAuthService->ensureCanDeleteAsset($user, $asset));
    }

    private function runCheck(callable $check): bool
    {
        try {
            $check();
            return true;
        } catch (\DomainException) {
            return false;
        }
    }
}
