<?php

namespace App\Application\Service\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Business\Service;
use App\Application\Service\DTO\UpdateServiceDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Application\Auth\Services\ServiceAuthorizationService;
use App\Models\Auth\User;

class UpdateService
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly ServiceRepositoryInterface $serviceRepo,
        private readonly ServiceAuthorizationService $authService
    ) {}

    /**
     * Executes the update service use case. It checks if the user has permission to update the service and then updates it with the provided data.
     * @param UpdateServiceDTO $dto The data transfer object containing the service ID and the data to update.
     * @param User $user The user performing the update operation.
     * @return Service The updated service.
     */
    public function execute(UpdateServiceDTO $dto, User $user): Service
    {
        return DB::transaction(function () use ($dto, $user) {
            $service = $this->serviceRepo->findForManagement($dto->id);

            $this->authService->ensureCanUpdateService($user, $service);

            $data = $dto->toArray();

            if ($dto->branch_ids !== null) {
                $businessBranches = $this->branchRepo->findByBusinessId($service->business_id);
                $validIds = $businessBranches->pluck('id')->toArray();

                $data['branch_ids'] = array_values(array_intersect($dto->branch_ids, $validIds));

                foreach ($businessBranches->whereIn('id', $data['branch_ids']) as $branch) {
                    $this->authService->ensureCanAssignServiceToBranch($user, $service->business, $branch);
                }
            }

            return $this->serviceRepo->update($service, $data);
        });
    }
}
