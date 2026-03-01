<?php
namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;

use App\Application\Business\DTO\CreateBranchDTO;
use App\Application\User\Services\AuthorizationService;

use App\Domain\Business\Repositories\BranchRepositoryInterface;
use App\Domain\Business\Repositories\BusinessRepositoryInterface;
use App\Domain\Business\Entities\Branch as DomainBranch;

class CreateBranch
{
    public function __construct(
        private AuthorizationService $authService,
        private BusinessRepositoryInterface $businessRepo,
        private BranchRepositoryInterface $branchRepo
    ) {}

    public function execute(CreateBranchDTO $dto, int $userId): DomainBranch
    {
        return DB::transaction(function () use ($dto, $userId) {

            $business = $this->businessRepo->findById($dto->business_id);
            if (!$business) {
                throw new \DomainException('Business not found.');
            }

            $this->authService->ensureCanCreateBranch($business, $userId);

            // Construct the domain entity first
            $branch = new DomainBranch(
                id: null,
                business_id: $business->id,
                name: $dto->name,
                type: $dto->type,
                address_line_1: $dto->address_line_1,
                address_line_2: $dto->address_line_2 ?? null,
                city: $dto->city,
                postal_code: $dto->postal_code,
                country: $dto->country,
                is_active: true,
            );

            // Persist via repository
            return $this->branchRepo->save($branch);
        });
    }
}