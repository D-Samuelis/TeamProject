<?php

namespace App\Repositories\Rule;

use Illuminate\Support\Collection;
use App\Models\Business\Rule;
use App\Domain\Rule\Interfaces\RuleRepositoryInterface;
use App\Application\Rule\DTO\UpdateRuleDTO;

class RuleRepository implements RuleRepositoryInterface
{
    public function findById(int $id): ?Rule
    {
        return Rule::find($id);
    }

    public function save(array $data): Rule
    {
        return Rule::create($data);
    }
}
