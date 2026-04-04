<?php

namespace App\Application\Service\UseCases;

use Illuminate\Support\Facades\DB;
use App\Application\Auth\Services\ServiceAuthorizationService;
use App\Application\Service\DTO\StoreBranchServiceDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\BranchServiceRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\BranchService;

class StoreBranchService
{
    public function __construct(
        private readonly ServiceRepositoryInterface       $serviceRepo,
        private readonly BranchRepositoryInterface        $branchRepo,
        private readonly BranchServiceRepositoryInterface $branchServiceRepo,
        private readonly ServiceAuthorizationService      $authService,
    ) {}

    public function execute(StoreBranchServiceDTO $dto, User $user): BranchService
    {
        return DB::transaction(function () use ($dto, $user) {
            $service = $this->serviceRepo->findForManagement($dto->service_id);
            $branch  = $this->branchRepo->findForManagement($dto->branch_id);

            if ($branch->business_id !== $service->business_id) {
                throw new \DomainException('Branch does not belong to this service\'s business.');
            }

            $this->authService->ensureCanAssignServiceToBranch($user, $service->business, $branch);

            $existing = $this->branchServiceRepo->findByServiceAndBranch($dto->service_id, $dto->branch_id);

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                // Update with the provided overrides
                return $this->branchServiceRepo->updateInstance($existing, $dto->toArray());
            }

            return $this->branchServiceRepo->createInstance($dto->toArray());
        });
    }
}
