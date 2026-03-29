<?php

namespace App\Application\Asset\UseCases;

use Illuminate\Support\Facades\DB;
use App\Application\Asset\DTO\UpdateAssetDTO;
use App\Application\Auth\Services\AssetAuthorizationService;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;

class UpdateAsset
{
    public function __construct(
        private AssetRepositoryInterface   $assetRepo,
        private ServiceRepositoryInterface $serviceRepo,
        private BranchRepositoryInterface  $branchRepo,
        private UserRepositoryInterface    $userRepo,
        private AssetAuthorizationService  $authService,
    ) {}

    public function execute(UpdateAssetDTO $dto, int $userId): void
    {
        $asset = $this->assetRepo->findById($dto->id);
        abort_if(! $asset, 404);

        $user = $this->userRepo->findById($userId);
        $this->authService->ensureCanUpdateAsset($user, $asset);

        DB::transaction(function () use ($dto, $asset) {
            $asset = $this->assetRepo->update($dto);

            if (!empty($dto->branch_ids)) {
                $valid = $this->branchRepo->findMultipleByIds($dto->branch_ids);
                $valid = array_intersect($dto->branch_ids, array_column($valid->toArray(), 'id'));
                $this->assetRepo->attachBranches($asset, $valid);
            }

            if (!empty($dto->service_ids)) {
                $valid = $this->serviceRepo->findMultipleByIds($dto->service_ids);
                $valid = array_intersect($dto->service_ids, array_column($valid->toArray(), 'id'));
                $this->assetRepo->attachServices($asset, $valid);
            }
        });
    }
}
