<?php
namespace App\Application\Business\UseCases;

use App\Application\Business\DTO\CreateBranchDTO;
use App\Application\Auth\AuthorizationService;
use App\Domain\Business\Repositories\BranchRepositoryInterface;
use App\Domain\Business\Repositories\BusinessRepositoryInterface;
use App\Domain\Business\Entities\Branch;

class CreateBranch
{
    public function __construct(
        private AuthorizationService $authService,
        private BusinessRepositoryInterface $businessRepo,
        private BranchRepositoryInterface $branchRepo
    ) {}

    public function execute(CreateBranchDTO $dto, int $userId): Branch
    {
        $business = $this->businessRepo->findById($dto->businessId);
        if (!$business) {
            throw new \DomainException('Business not found.');
        }

        $this->authService->ensureCanCreateBranch($business, $userId);

        $branch = $this->branchRepo->create([
            'business_id' => $business->id,
            'name' => $dto->name,
            'type' => $dto->type,
            'address_line1' => $dto->addressLine1,
            'address_line2' => $dto->addressLine2 ?? null,
            'city' => $dto->city,
            'postal_code' => $dto->postalCode,
            'country' => $dto->country,
            'is_active' => true,
        ]);

        return $branch;
    }
}