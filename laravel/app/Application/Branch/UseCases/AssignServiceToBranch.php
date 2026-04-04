<?php

namespace App\Application\Branch\UseCases;

use App\Models\Business\Service;
use App\Models\Business\BranchService;

class AssignServiceToBranchUseCase
{
    /**
     * Assign one or multiple service templates to a branch.
     *
     * @param int $branchId
     * @param int[]  $serviceIds
     * @param bool   $enabled
     * @return BranchService[]
     */
    public function execute(int $branchId, array $serviceIds, bool $enabled = true): array
    {
        $assigned = [];

        foreach ($serviceIds as $id) {
            $service = Service::findOrFail($id);

            $assigned[] = BranchService::firstOrCreate(
                [
                    'branch_id'  => $branchId,
                    'service_id' => $service->id,
                ],
                [
                    'is_enabled' => $enabled,
                ]
            );
        }

        return $assigned;
    }
}