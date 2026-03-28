<?php

namespace App\Application\Service\UseCases;

use App\Application\Auth\Services\ServiceAuthorizationService;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;

class AssignServiceToBranch
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo,
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly ServiceAuthorizationService $authService
    ) {}

    public function execute(int $serviceId, int $branchId, User $user): void
    {
        DB::transaction(function () use ($serviceId, $branchId, $user) {
            $service = $this->serviceRepo->findForManagement($serviceId);
            $branch = $this->branchRepo->findForManagement($branchId);

            if ($branch->business_id !== $service->business_id) {
                throw new \DomainException('Branch does not belong to this service\'s business.');
            }

            $this->authService->ensureCanAssignServiceToBranch($user, $service->business, $branch);

            $service->branches()->syncWithoutDetaching([$branchId]);
        });
    }
}
