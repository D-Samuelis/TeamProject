<?php

namespace App\Application\Rule\UseCases;

use App\Application\Auth\Services\AssetAuthorizationService;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;
use App\Models\Business\Rule;
use App\Application\Rule\DTO\UpdateRuleDTO;
use App\Domain\Rule\Interfaces\RuleRepositoryInterface;

class UpdateRule
{
    public function __construct(
        private AssetRepositoryInterface $assetRepo,
        private RuleRepositoryInterface $ruleRepo,
        private AssetAuthorizationService  $authService,
    ) {}

    public function execute(UpdateRuleDTO $dto, User $user): Rule
    {
        $rule = $this->ruleRepo->findById($dto->id);
        abort_if(!$rule, 404);

        $asset = $this->assetRepo->findForManagement($rule->asset_id);

        $this->authService->ensureCanUpdateAsset($user, $asset);

        return DB::transaction(fn() => $this->ruleRepo->update($rule, $dto->toArray()));
    }
}
