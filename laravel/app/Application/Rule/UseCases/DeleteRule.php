<?php

namespace App\Application\Rule\UseCases;

use App\Domain\Rule\Interfaces\RuleRepositoryInterface;

class DeleteRule
{
    public function __construct(
        private readonly RuleRepositoryInterface $ruleRepo
    ) {}

    public function execute(int $ruleId, int $userId): void
    {
        $rule = $this->ruleRepo->findById($ruleId);
        abort_if(!$rule, 404);
        $this->ruleRepo->delete($rule);
    }
}
