<?php

namespace App\Application\Asset\UseCases;

use Illuminate\Support\Facades\DB;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Application\Auth\Services\AssetAuthorizationService;

class DeleteAsset
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private AssetAuthorizationService $assetAuthService,
        private AssetRepositoryInterface $assetRepo
    ) {}

    public function execute(int $assetId, int $userId): void
    {
        DB::transaction(function () use ($assetId, $userId) {
            $asset = $this->assetRepo->findById($assetId);
            $user = $this->userRepo->findById($userId);

            $this->assetAuthService->ensureCanDeleteAsset($user, $asset);

            $this->assetRepo->delete($asset);
        });
    }
}
