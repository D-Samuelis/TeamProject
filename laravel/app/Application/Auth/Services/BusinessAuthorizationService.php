<?php

namespace App\Application\Auth\Services;

use DomainException;
use App\Domain\Business\Enums\BusinessStateEnum;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Business;

class BusinessAuthorizationService
{
    public function __construct(private BusinessRepositoryInterface $businessRepo, private UserRepositoryInterface $userRepo) {}

    /**
     * Check if a user is allowed to create a new business.
     */
    public function ensureCanCreateBusiness(User $user): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($this->businessRepo->existsOwner($user->id)) {
            throw new DomainException('You already own a business and cannot create another one.');
        }
    }

    /**
     * Check if a user can view business details.
     */
    public function ensureCanViewBusiness(User $user, Business $business): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $role = $this->userRepo->getBusinessRole($user, $business);

        if (!$role) {
            throw new DomainException('You do not have permission to view this business.');
        }
    }

    /**
     * Check if a user can update business information.
     */
    public function ensureCanUpdateBusiness(User $user, Business $business): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $role = $this->userRepo->getBusinessRole($user, $business);

        if (!$role || !$role->canUpdate()) {
            throw new DomainException('Only the owner or managers can update the business.');
        }
    }

    /**
     * Check if a user can delete a business.
     */
    public function ensureCanDeleteBusiness(User $user, Business $business): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $role = $this->userRepo->getBusinessRole($user, $business);

        if (!$role || !$role->canDelete()) {
            throw new DomainException('Only the owner can delete the business.');
        }
    }

    /**
     * Check if a user can publish a business.
     */
    public function ensureCanPublishBusiness(User $user, Business $business): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $role = $this->userRepo->getBusinessRole($user, $business);

        if (!$role || !$role->canPublish()) {
            throw new DomainException('Only the owner or managers can publish the business.');
        }
    }

    /**
     * Non-role based check for the state of the business itself.
     */
    public function ensureBusinessIsApproved(Business $business): void
    {
        if ($business->state !== BusinessStateEnum::APPROVED) {
            throw new DomainException('Business must be approved to perform this action.');
        }
    }
}
