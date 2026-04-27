<?php

namespace App\Application\Service\UseCases;

use App\Application\Auth\Services\ServiceAuthorizationService;
use Illuminate\Support\Facades\DB;
use App\Models\Business\Service;
use App\Application\Service\DTO\StoreServiceDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Auth\User;

class StoreService
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly ServiceRepositoryInterface $serviceRepo,
        private readonly ServiceAuthorizationService $authService
    ) {}

    /**
     * Executes the store service use case. It checks if the user has permission to create a service and then creates it with the provided data.
     * @param StoreServiceDTO $dto The data transfer object containing the data for the new service.
     * @param User $user The user performing the create operation.
     * @return Service The created service.
     */
    public function execute(StoreServiceDTO $dto, User $user): Service
    {
        return DB::transaction(function () use ($dto, $user) {
            $business = $this->businessRepo->findForManagement($dto->business_id);

            $this->authService->ensureCanCreateService($user, $business);

            if (!empty($dto->branch_ids)) {
                $validBranches = $this->branchRepo->findByBusinessId($business->id);
                $validIds = $validBranches->pluck('id')->toArray();

                $dto->branch_ids = array_values(array_intersect($dto->branch_ids, $validIds));

                foreach ($validBranches->whereIn('id', $dto->branch_ids) as $branch) {
                    $this->authService->ensureCanAssignServiceToBranch($user, $business, $branch);
                }
            }

            return $this->serviceRepo->save($dto->toArray());
        });
    }
}
