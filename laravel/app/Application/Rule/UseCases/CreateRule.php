<?php

namespace App\Application\Rule\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Business\Rule;
use App\Application\Rule\DTO\CreateRuleDTO;
use App\Domain\Rule\Interfaces\RuleRepositoryInterface;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;

class CreateRule
{
    public function __construct(
        private AssetRepositoryInterface $assetRepo,
        private RuleRepositoryInterface $ruleRepo
    ) {}

    public function execute(CreateRuleDTO $dto, int $userId): Rule
    {
        return DB::transaction(function () use ($dto) {
            $maxPriority = $this->ruleRepo->getMaxPriority($dto->asset_id);

            $data = $dto->toArray();
            $data['priority'] = $maxPriority + 1;

            return $this->ruleRepo->save($data);
        });
    }
}
