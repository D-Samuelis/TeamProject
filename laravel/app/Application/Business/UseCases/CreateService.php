<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;

use App\Application\Business\DTO\CreateServiceDTO;
use App\Application\User\Services\AuthorizationService;

use App\Domain\Business\Entities\Service;
use App\Domain\Business\Repositories\BranchRepositoryInterface;
use App\Domain\Business\Repositories\BusinessRepositoryInterface;
use App\Domain\Business\Repositories\ServiceRepositoryInterface;

class CreateService
{
    public function __construct(
        private AuthorizationService $authService,
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

            $this->authService->ensureCanManageBusiness($business, $userId);

            $service = new Service(
                id: null,
                business_id: $business->id,
                name: $dto->name,
                description: $dto->description,
                duration_minutes: $dto->duration_minutes,
                price: $dto->price,
                location_type: $dto->location_type,
                is_active: true,
            );

            $service = $this->serviceRepo->save($service);

            // Only attach via repository, not domain entity
            if (!empty($dto->branch_ids)) {
                $validBranchIds = $this->branchRepo->findByBusinessId($business->id);
                $validBranchIds = array_intersect($dto->branch_ids, array_column($validBranchIds, 'id'));

                $this->serviceRepo->attachBranches($service, $validBranchIds);
            }

            return $service;
        });
    }
}
