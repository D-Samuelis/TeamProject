<?php

namespace App\Application\Rule\UseCases;

use Illuminate\Support\Facades\DB;
use App\Domain\Rule\Interfaces\RuleRepositoryInterface;

class ReorderRule
{
    public function __construct(
        private readonly RuleRepositoryInterface $ruleRepo,
    ) {}

    /**
     * Move a rule up (lower priority number) or down (higher priority number)
     * by swapping priorities with its neighbour.
     */
    public function execute(int $ruleId, string $direction, int $userId): void
    {
        $rule = $this->ruleRepo->findById($ruleId);
        abort_if(! $rule, 404);

        DB::transaction(function () use ($rule, $direction) {
            // Find the adjacent rule for this asset in the requested direction
            $neighbour = $direction === 'up'
                ? $this->ruleRepo->findByPriorityAbove($rule->asset_id, $rule->priority)
                : $this->ruleRepo->findByPriorityBelow($rule->asset_id, $rule->priority);

            abort_if(! $neighbour, 422); // already at top/bottom

            // Swap priorities — use a temp value to avoid unique constraint collision
            $temp             = $rule->priority;
            $neighbourPriority = $neighbour->priority;

            // Set rule to a temp value outside normal range
            $this->ruleRepo->update($rule,     ['priority' => 99999]);
            $this->ruleRepo->update($neighbour, ['priority' => $temp]);
            $this->ruleRepo->update($rule,     ['priority' => $neighbourPriority]);
        });
    }
}
