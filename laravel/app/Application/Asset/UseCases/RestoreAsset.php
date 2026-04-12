<?php

namespace App\Application\Asset\UseCases;

use Illuminate\Support\Facades\DB;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Application\Auth\Services\AssetAuthorizationService;
use App\Models\Auth\User;

class RestoreAsset
{
    public function __construct(
        private readonly AssetAuthorizationService $authService,
        private readonly AssetRepositoryInterface $assetRepo
    ) {}

    public function execute(int $assetId, User $user): void
    {
        DB::transaction(function () use ($assetId, $user) {
            $asset = $this->assetRepo->findForManagement($assetId);

            $this->authService->ensureCanUpdateAsset($user, $asset);

            $this->assetRepo->restore($asset);
        });
    }
}
