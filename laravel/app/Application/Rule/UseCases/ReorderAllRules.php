<?php

namespace App\Application\Rule\UseCases;

use App\Domain\Rule\Interfaces\RuleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ReorderAllRules
{
    public function execute(array $orderedIds, int $userId): void
    {
        DB::transaction(function () use ($orderedIds) {
            DB::table('rules')
                ->whereIn('id', $orderedIds)
                ->update(['priority' => DB::raw('priority + 10000')]);

            foreach ($orderedIds as $index => $id) {
                DB::table('rules')
                    ->where('id', $id)
                    ->update(['priority' => $index + 1]);
            }
        });
    }
}
