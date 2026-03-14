<?php

namespace App\Repositories\Rule;

use App\Models\Business\Rule;
use App\Domain\Rule\Interfaces\RuleRepositoryInterface;

class RuleRepository implements RuleRepositoryInterface
{
    public function findById(int $id): ?Rule
    {
        return Rule::withTrashed()->find($id);
    }

    public function save(array $data): Rule
    {
        return Rule::create($data);
    }

    public function update(Rule $rule, array $data): Rule
    {
        $rule->update($data);
        return $rule->fresh();
    }

    public function delete(Rule $rule): void
    {
        $rule->delete(); // soft delete
    }

    public function restore(Rule $rule): void
    {
        $rule->restore();
    }
}
