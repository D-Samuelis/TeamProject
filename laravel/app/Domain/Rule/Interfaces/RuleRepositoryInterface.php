<?php

namespace App\Domain\Rule\Interfaces;

use Illuminate\Support\Collection;
use App\Models\Business\Rule;
use App\Application\Rule\DTO\UpdateAssetDTO;

interface RuleRepositoryInterface
{
    public function findById(int $id): ?Rule;

    public function save(array $data): Rule;
}
