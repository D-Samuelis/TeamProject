<?php

namespace App\Application\Service\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Business\Service;
use App\Application\Service\DTO\StoreServiceDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;

class StoreService
{
    public function __construct(
        private BusinessRepositoryInterface $businessRepo,
        private BranchRepositoryInterface $branchRepo,
        private ServiceRepositoryInterface $serviceRepo,
    ) {}

    public function execute(StoreServiceDTO $dto, int $userId): Service
    {
        return DB::transaction(function () use ($dto, $userId) {
            $business = $this->businessRepo->findActive($dto->business_id);
            if (!$business) {
                throw new \DomainException('Business not found.');
            }

            //$this->authService->ensureCanManageBusiness($business, $userId);

            $service = $this->serviceRepo->save($dto->toArray());

            if (!empty($dto->branch_ids)) {
                $validBranchIds = $this->branchRepo->findByBusinessId($business->id);
                $validBranchIds = array_intersect($dto->branch_ids, array_column($validBranchIds->toArray(), 'id'));

                $this->serviceRepo->attachBranches($service, $validBranchIds);
            }

            return $service;
        });
    }
}
