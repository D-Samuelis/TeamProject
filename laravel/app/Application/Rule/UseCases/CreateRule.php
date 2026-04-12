<?php

namespace App\Application\Rule\UseCases;

use App\Application\Auth\Services\AssetAuthorizationService;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;
use App\Models\Business\Rule;
use App\Application\Rule\DTO\CreateRuleDTO;
use App\Domain\Rule\Interfaces\RuleRepositoryInterface;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;

class CreateRule
{
    public function __construct(
        private AssetRepositoryInterface $assetRepo,
        private RuleRepositoryInterface $ruleRepo,
        private AssetAuthorizationService  $authService,
    ) {}

    public function execute(CreateRuleDTO $dto, User $user): Rule
    {
        $asset = $this->assetRepo->findForManagement($dto->asset_id);

        $this->authService->ensureCanUpdateAsset($user, $asset);

        return DB::transaction(function () use ($dto) {
            $maxPriority = $this->ruleRepo->getMaxPriority($dto->asset_id);

            $data = $dto->toArray();
            $data['priority'] = $maxPriority + 1;

            return $this->ruleRepo->save($data);
        });
    }
}
