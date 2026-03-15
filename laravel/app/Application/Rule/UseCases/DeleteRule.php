<?php

namespace App\Application\Rule\UseCases;

use App\Domain\Rule\Interfaces\RuleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeleteRule
{
    public function __construct(
        private readonly RuleRepositoryInterface $ruleRepo
    ) {}

    public function execute(int $ruleId, int $userId): void
    {
        $rule = $this->ruleRepo->findById($ruleId);
        abort_if(!$rule, 404);

        DB::transaction(function () use ($rule) {
            $deletedPriority = $rule->priority;
            $this->ruleRepo->delete($rule);
            $this->ruleRepo->renumberPriorities($rule->asset_id, $deletedPriority);
        });
    }
}
