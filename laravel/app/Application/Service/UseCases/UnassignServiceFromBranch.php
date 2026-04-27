<?php

namespace App\Application\Service\UseCases;

use App\Application\Auth\Services\ServiceAuthorizationService;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;

class UnassignServiceFromBranch
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo,
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly ServiceAuthorizationService $authService
    ) {}

    /**
     * Executes the unassign service from branch use case. It checks if the service and branch exist and if the user has permission to unassign the service from the branch, then unassigns the service from the branch.
     * @param int $serviceId The ID of the service to unassign.
     * @param int $branchId The ID of the branch from which to unassign the service.
     * @param User $user The user performing the unassignment operation.
     * @return void
     */
    public function execute(int $serviceId, int $branchId, User $user): void
    {
        DB::transaction(function () use ($serviceId, $branchId, $user) {
            $service = $this->serviceRepo->findForManagement($serviceId);
            $branch = $this->branchRepo->findForManagement($branchId);
            $this->authService->ensureCanAssignServiceToBranch($user, $service->business, $branch);
            $service->branches()->detach($branchId);
        });
    }
}
