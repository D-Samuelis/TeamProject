<?php

namespace App\Application\Rule\UseCases;

use Illuminate\Foundation\Http\FormRequest;
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
        return DB::transaction(function () use ($dto, $userId) {
            $data = $dto->toArray();
            \Log::info('Saving rule', $data); // <-- log the data
            return $this->ruleRepo->save($data);
        });
    }
}
