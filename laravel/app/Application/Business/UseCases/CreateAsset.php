<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;

use App\Application\Business\DTO\CreateAssetDTO;
use App\Application\Auth\Services\AuthorizationService;

use App\Domain\Business\Entities\Branch;
use App\Domain\Business\Entities\Service;

class CreateAsset
{
    public function __construct(private AuthorizationService $authService) {}

    public function execute(CreateAssetDTO $dto, int $userId): Asset
    {
        return DB::transaction(function () use ($dto, $userId) {
            $branch = Branch::findOrFail($dto->branchId);

            $this->authService->ensureCanManageBranch($business->id, $userId);

            $asset = Asset::create([
                'branch_id' => $business->id,
                'service_id' => $dto->serviceId,
                'name' => $dto->name,
                'description' => $dto->description
            ]);

            return $asset;
        });
    }
}
