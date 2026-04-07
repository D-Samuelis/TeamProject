<?php

namespace App\Application\Asset\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Business\Asset;
use App\Application\Asset\DTO\CreateAssetDTO;
use App\Application\Auth\Services\AssetAuthorizationService;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;

class CreateAsset
{
    public function __construct(
        private AssetRepositoryInterface   $assetRepo,
        private ServiceRepositoryInterface $serviceRepo,
        private BranchRepositoryInterface  $branchRepo,
        private UserRepositoryInterface    $userRepo,
        private AssetAuthorizationService  $authService,
    ) {}

    public function execute(CreateAssetDTO $dto, int $userId): Asset
    {
        $user = $this->userRepo->findById($userId);
        $this->authService->ensureCanCreateAsset($user);

        return DB::transaction(function () use ($dto) {
            $asset = $this->assetRepo->save($dto->toArray());

            if (!empty($dto->service_ids)) {
                $valid = $this->serviceRepo->findMultipleByIds($dto->service_ids);
                $valid = array_intersect($dto->service_ids, array_column($valid->toArray(), 'id'));
                $this->assetRepo->attachServices($asset, $valid);
            }

            return $asset;
        });
    }
}
