<?php

namespace App\Application\Service\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;

use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Application\Auth\Services\ServiceAuthorizationService;

class AssignServiceToBranch
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo,
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly ServiceAuthorizationService $authService
    ) {}

    /**
     * Executes the assign service to branch use case. It checks if the service and branch exist and if the user has permission to assign the service to the branch, then assigns the service to the branch.
     * @param int $serviceId The ID of the service to assign.
     * @param int $branchId The ID of the branch to which to assign the service.
     * @param User $user The user performing the assignment operation.
     * @return void
     */
    public function execute(int $serviceId, int $branchId, User $user): void
    {
        DB::transaction(function () use ($serviceId, $branchId, $user) {
            $service = $this->serviceRepo->findForManagement($serviceId);
            $branch  = $this->branchRepo->findForManagement($branchId);

            if ($branch->business_id !== $service->business_id) {
                throw new \InvalidArgumentException('Branch does not belong to this service\'s business.');
            }

            $this->authService->ensureCanAssignServiceToBranch($user, $service->business, $branch);
            $service->branches()->syncWithoutDetaching([$branchId]);
        });
    }
}
