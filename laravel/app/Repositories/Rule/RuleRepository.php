<?php

namespace App\Repositories\Rule;

use App\Models\Business\Rule;
use App\Domain\Rule\Interfaces\RuleRepositoryInterface;

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

    public function update(Rule $rule, array $data): Rule
    {
        $rule->update($data);
        return $rule->fresh();
    }

    public function delete(Rule $rule): void
    {
        $rule->delete();
    }

    public function restore(Rule $rule): void
    {
        $rule->restore();
    }

    public function getMaxPriority(int $assetId): int
    {
        return Rule::where('asset_id', $assetId)->max('priority') ?? 0;
    }

    public function findByPriorityAbove(int $assetId, int $currentPriority): ?Rule
    {
        return Rule::where('asset_id', $assetId)
            ->where('priority', '<', $currentPriority)
            ->orderByDesc('priority')
            ->first();
    }

    public function findByPriorityBelow(int $assetId, int $currentPriority): ?Rule
    {
        return Rule::where('asset_id', $assetId)
            ->where('priority', '>', $currentPriority)
            ->orderBy('priority')
            ->first();
    }

    public function renumberPriorities(int $assetId, int $deletedPriority): void
    {
        Rule::where('asset_id', $assetId)
            ->where('priority', '>', $deletedPriority)
            ->decrement('priority');
    }

    public function shiftPriorities(array $ids, int $offset): void
    {
        Rule::whereIn('id', $ids)->increment('priority', $offset);
    }

    public function setPriority(int $id, int $priority): void
    {
        Rule::where('id', $id)->update(['priority' => $priority]);
    }
}
