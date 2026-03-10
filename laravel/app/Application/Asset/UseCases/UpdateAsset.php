<?php

namespace App\Application\Asset\UseCases;

use App\Models\Business\Asset;
use App\Application\Asset\DTO\UpdateAssetDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;

class UpdateAsset
{
    public function __construct(
        private AssetRepositoryInterface $assetRepo,
        private ServiceRepositoryInterface $serviceRepo,
        private BranchRepositoryInterface $branchRepo
    ) {}

    public function execute(UpdateAssetDTO $dto, int $userId): void
    {
        $asset = $this->assetRepo->update($dto);

        if (!empty($dto->branch_ids)) {
            $validBranchIds = $this->branchRepo->findMultipleByIds($dto->branch_ids);
            $validBranchIds = array_intersect($dto->branch_ids, array_column($validBranchIds->toArray(), 'id'));

            $this->assetRepo->attachBranches($asset, $validBranchIds);
        }

        if (!empty($dto->service_ids)) {
            $validBranchIds = $this->serviceRepo->findMultipleByIds($dto->service_ids);
            $validBranchIds = array_intersect($dto->service_ids, array_column($validBranchIds->toArray(), 'id'));

            $this->assetRepo->attachServices($asset, $validBranchIds);
        }
    }
}
