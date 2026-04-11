<?php

namespace App\Application\Asset\UseCases;

use App\Application\Auth\Services\AssetAuthorizationService;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Asset;

class GetAsset
{
    public function __construct(
        private readonly AssetRepositoryInterface  $assetRepo,
        private readonly AssetAuthorizationService $authService,
    ) {}

    public function execute(int $assetId, ?User $user = null): Asset
    {
        if ($user) {
            $asset = $this->assetRepo->findForManagement($assetId);
            abort_if(! $asset, 404);
            $this->authService->ensureCanViewAsset($user, $asset);
            return $asset;
        }

        return $this->assetRepo->findActive($assetId);
    }
}
