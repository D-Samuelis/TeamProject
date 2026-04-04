<?php

namespace App\Application\Service\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Business\Service;
use App\Application\Service\DTO\StoreServiceDTO;
use App\Application\Auth\Services\ServiceAuthorizationService;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\Service\Interfaces\BranchServiceRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Auth\User;

class StoreService
{
    public function __construct(
        private readonly BusinessRepositoryInterface       $businessRepo,
        private readonly BranchRepositoryInterface        $branchRepo,
        private readonly ServiceRepositoryInterface       $serviceRepo,
        private readonly BranchServiceRepositoryInterface $branchServiceRepo,
        private readonly ServiceAuthorizationService      $authService,
    ) {}

    public function execute(StoreServiceDTO $dto, User $user): Service
    {
        return DB::transaction(function () use ($dto, $user) {
            $business = $this->businessRepo->findForManagement($dto->business_id);

            $this->authService->ensureCanCreateService($user, $business);

            // Create the template (only real service columns, no branch_ids)
            $service = $this->serviceRepo->save($dto->toTemplateArray());

            // Optionally instantiate the service on selected branches right away
            if (! empty($dto->branch_ids)) {
                $businessBranches = $this->branchRepo->findByBusinessId($business->id);
                $validIds         = $businessBranches->pluck('id')->toArray();
                $toAssign         = array_values(array_intersect($dto->branch_ids, $validIds));

                foreach ($businessBranches->whereIn('id', $toAssign) as $branch) {
                    $this->authService->ensureCanAssignServiceToBranch($user, $business, $branch);

                    $this->branchServiceRepo->createInstance([
                        'branch_id'  => $branch->id,
                        'service_id' => $service->id,
                        'is_enabled' => true,
                    ]);
                }
            }

            return $this->serviceRepo->findForManagement($service->id);
        });
    }
}
