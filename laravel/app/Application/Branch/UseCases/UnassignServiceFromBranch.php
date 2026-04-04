<?php

namespace App\Application\Branch\UseCases;

use App\Models\Business\BranchService;

class UnassignServiceFromBranchUseCase
{
    /**
     * Unassign one or multiple services from a branch.
     *
     * @param int $branchId
     * @param int[]  $serviceIds
     * @return int Number of deleted assignments
     */
    public function execute(int $branchId, array $serviceIds): int
    {
        return BranchService::where('branch_id', $branchId)
            ->whereIn('service_id', $serviceIds)
            ->delete();
    }
}
