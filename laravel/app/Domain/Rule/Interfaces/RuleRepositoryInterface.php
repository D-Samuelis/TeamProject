<?php

namespace App\Domain\Rule\Interfaces;

use Illuminate\Support\Collection;
use App\Models\Business\Rule;

interface RuleRepositoryInterface
{
    public function findById(int $id): ?Rule;

    public function save(array $data): Rule;

    public function update(Rule $rule, array $data): Rule;

    public function delete(Rule $rule): void;

    public function restore(Rule $rule): void;

    public function getMaxPriority(int $assetId): int;

    public function findByPriorityAbove(int $assetId, int $currentPriority): ?Rule;

    public function findByPriorityBelow(int $assetId, int $currentPriority): ?Rule;

    public function renumberPriorities(int $assetId, int $deletedPriority): void;

    public function shiftPriorities(array $ids, int $offset): void;

    public function setPriority(int $id, int $priority): void;
}
