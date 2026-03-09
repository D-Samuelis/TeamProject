<?php

namespace App\Application\Service\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Business\Service;
use App\Application\Service\DTO\UpdateServiceDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Application\Auth\Services\ServiceAuthorizationService;

class UpdateService
{
    public function __construct(
        private BranchRepositoryInterface $branchRepo,
        private ServiceRepositoryInterface $serviceRepo,
        //private ServiceAuthorizationService $authService
    ) {}

    public function execute(UpdateServiceDTO $dto, int $userId): Service
    {
        return DB::transaction(function () use ($dto, $userId) {
            $service = $this->serviceRepo->findForManagement($dto->id);

            //$this->authService->ensureCanUpdateService($userId, $service);

            $data = $dto->toArray();

            if ($dto->branch_ids !== null) {
                $businessBranches = $this->branchRepo->findByBusinessId($service->business_id);
                $data['branch_ids'] = array_intersect(
                    $dto->branch_ids,
                    $businessBranches->pluck('id')->toArray()
                );
            }

            return $this->serviceRepo->update($service, $data);
        });
    }
}
