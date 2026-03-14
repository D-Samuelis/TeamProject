<?php

namespace App\Application\Rule\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Business\Rule;
use App\Application\Rule\DTO\UpdateRuleDTO;
use App\Domain\Rule\Interfaces\RuleRepositoryInterface;

class UpdateRule
{
    public function __construct(
        private readonly RuleRepositoryInterface $ruleRepo
    ) {}

    public function execute(UpdateRuleDTO $dto, int $userId): Rule
    {
        return DB::transaction(function () use ($dto) {
            $rule = $this->ruleRepo->findById($dto->id);
            abort_if(!$rule, 404);
            return $this->ruleRepo->update($rule, $dto->toArray());
        });
    }
}
