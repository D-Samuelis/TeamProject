<?php

namespace App\Application\Service\UseCases;

use App\Application\Auth\Services\ServiceAuthorizationService;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\BranchServiceRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;

class UnassignServiceFromBranch
{
    public function __construct(
        private readonly ServiceRepositoryInterface       $serviceRepo,
        private readonly BranchRepositoryInterface        $branchRepo,
        private readonly BranchServiceRepositoryInterface $branchServiceRepo,
        private readonly ServiceAuthorizationService      $authService,
    ) {}

    public function execute(int $serviceId, int $branchId, User $user): void
    {
        DB::transaction(function () use ($serviceId, $branchId, $user) {
            $service      = $this->serviceRepo->findForManagement($serviceId);
            $branch       = $this->branchRepo->findForManagement($branchId);
            $branchService = $this->branchServiceRepo->findByServiceAndBranch($serviceId, $branchId);

            if (! $branchService || $branchService->trashed()) {
                return; // Already gone, nothing to do
            }

            $this->authService->ensureCanAssignServiceToBranch($user, $service->business, $branch);

            // Soft-delete — appointments keep their FK intact, instance can be restored
            $this->branchServiceRepo->deleteInstance($branchService);
        });
    }
}
