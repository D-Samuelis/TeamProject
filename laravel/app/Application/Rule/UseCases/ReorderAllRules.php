<?php

namespace App\Application\Rule\UseCases;

use App\Application\Auth\Services\AssetAuthorizationService;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Domain\Rule\Interfaces\RuleRepositoryInterface;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;

class ReorderAllRules
{
    public function __construct(
        private AssetRepositoryInterface  $assetRepo,
        private RuleRepositoryInterface   $ruleRepo,
        private AssetAuthorizationService $authService,
    ) {}

    public function execute(array $orderedIds, User $user): void
    {
        if (empty($orderedIds)) return;

        $rule = $this->ruleRepo->findById($orderedIds[0]);
        abort_if(!$rule, 404);

        $asset = $this->assetRepo->findForManagement($rule->asset_id);

        $this->authService->ensureCanUpdateAsset($user, $asset);

        DB::transaction(function () use ($orderedIds) {
            $this->ruleRepo->shiftPriorities($orderedIds, 10000);

            foreach ($orderedIds as $index => $id) {
                $this->ruleRepo->setPriority($id, $index + 1);
            }
        });
    }
}
