<?php

namespace App\Application\Rule\UseCases;

use App\Application\Auth\Services\AssetAuthorizationService;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Domain\Rule\Interfaces\RuleRepositoryInterface;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;

class DeleteRule
{
    public function __construct(
        private AssetRepositoryInterface $assetRepo,
        private RuleRepositoryInterface $ruleRepo,
        private AssetAuthorizationService  $authService,
    ) {}

    public function execute(int $ruleId, User $user): void
    {
        $rule = $this->ruleRepo->findById($ruleId);
        abort_if(!$rule, 404);

        $asset = $this->assetRepo->findForManagement($rule->asset_id);

        $this->authService->ensureCanUpdateAsset($user, $asset);

        DB::transaction(function () use ($rule) {
            $deletedPriority = $rule->priority;
            $this->ruleRepo->delete($rule);
            $this->ruleRepo->renumberPriorities($rule->asset_id, $deletedPriority);
        });
    }
}
