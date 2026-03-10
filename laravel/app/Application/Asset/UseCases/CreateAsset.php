<?php

namespace App\Application\Asset\UseCases;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Business\Asset;
use App\Application\Asset\DTO\CreateAssetDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;

class CreateAsset
{
    public function __construct(
        private AssetRepositoryInterface $assetRepo,
        private ServiceRepositoryInterface $serviceRepo,
        private BranchRepositoryInterface $branchRepo
    ) {}

    public function execute(CreateAssetDTO $dto, int $userId): Asset
    {
        return DB::transaction(function () use ($dto, $userId) {
            $asset = $this->assetRepo->save($dto->toArray());

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

            return $asset;
        });
    }
}
