<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Business\Service;

use App\Application\Business\DTO\CreateServiceDTO;

use App\Domain\Business\Repositories\BranchRepositoryInterface;
use App\Domain\Business\Repositories\BusinessRepositoryInterface;
use App\Domain\Business\Repositories\ServiceRepositoryInterface;

class CreateService
{
    public function __construct(
        private BusinessRepositoryInterface $businessRepo,
        private ServiceRepositoryInterface $serviceRepo,
        private BranchRepositoryInterface $branchRepo
    ) {}

    public function execute(CreateServiceDTO $dto, int $userId): Service
    {
        return DB::transaction(function () use ($dto, $userId) {
            $business = $this->businessRepo->findById($dto->business_id);
            if (!$business) {
                throw new \DomainException('Business not found.');
            }

            //$this->authService->ensureCanManageBusiness($business, $userId);

            $service = $this->serviceRepo->save($dto->toArray());

            // Only attach via repository, not domain entity
            if (!empty($dto->branch_ids)) {
                $validBranchIds = $this->branchRepo->findByBusinessId($business->id);
                $validBranchIds = array_intersect($dto->branch_ids, array_column($validBranchIds->toArray(), 'id'));

                $this->serviceRepo->attachBranches($service, $validBranchIds);
            }

            return $service;
        });
    }
}
