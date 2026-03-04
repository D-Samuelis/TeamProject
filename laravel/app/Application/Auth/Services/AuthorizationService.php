<?php

namespace App\Application\Auth\Services;

use DomainException;

use App\Domain\Business\Entities\Business;
use App\Domain\Business\Repositories\BusinessRepositoryInterface;
use App\Domain\Business\Repositories\BranchRepositoryInterface;

class AuthorizationService
{
    public function __construct(
        private BusinessRepositoryInterface $businessRepo,
        private BranchRepositoryInterface $branchRepo
    ) {}

    // Business checks
    public function ensureCanCreateBranch(Business $business, int $userId): void
    {
        if (! $this->isOwner($business, $userId)) {
            throw new DomainException('Only the business owner can create branches.');
        }

        $this->ensureBusinessIsApproved($business);
    }

    public function ensureCanManageBusiness(Business $business, int $userId): void
    {
        if (! $this->isOwner($business, $userId)) {
            throw new DomainException('Not allowed to manage this business.');
        }

        $this->ensureBusinessIsApproved($business);
    }

    public function ensureCanCreateBusiness(int $userId): void
    {
        $ownsBusiness = $this->businessRepo->existsOwner($userId);

        if ($ownsBusiness) {
            throw new DomainException('You already own a business.');
        }
    }

    // Branch checks
    public function ensureCanManageBranch(int $branchId, Business $business, int $userId): void
    {
        // Owner can manage any branch
        if ($this->isOwner($business, $userId)) {
            $this->ensureBusinessIsApproved($business);
            return;
        }

        // Otherwise, must be assigned to the branch
        $branchAssignments = $this->branchRepo->getAssignments($branchId); // returns array of user_ids
        if (! in_array($userId, $branchAssignments)) {
            throw new DomainException('Not allowed to manage this branch.');
        }

        $this->ensureBusinessIsApproved($business);
    }

    // Helpers
    private function isOwner(Business $business, int $userId): bool
    {
        // Could also cache if needed
        $owners = $this->businessRepo->getOwners($business->id); // array of user_ids
        return in_array($userId, $owners);
    }

    private function ensureBusinessIsApproved(Business $business): void
    {
        if (! $business->isApproved()) {
            throw new DomainException('Business is not approved yet.');
        }
    }

    public function canManageBranch(int $branchId, Business $business, int $userId): bool
    {
        return $this->isOwner($business, $userId)
            || in_array($userId, $this->branchRepo->getAssignments($branchId));
    }
}
