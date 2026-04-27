<?php

namespace App\Application\Auth\Services;

use App\Models\Auth\User;
use App\Models\Business\Business;

use App\Domain\Business\Enums\BusinessStateEnum;

use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;

use App\Exceptions\UnauthorizedException;

class BusinessAuthorizationService
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly UserRepositoryInterface $userRepo
    ) {}

    /**
     * Check if a user is allowed to create a new business.
     */
    public function ensureCanCreateBusiness(User $user): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($this->businessRepo->existsOwner($user->id)) {
            throw new UnauthorizedException('You already own a business and cannot create another one.');
        }
    }

    /**
     * Check if a user can view business details.
     */
    public function ensureCanViewBusiness(?User $user, Business $business): void
    {
        // Public
        if ($business->is_published) {
            return;
        }

        // Private
        if (!$user) {
            throw new UnauthorizedException('This business is private.');
        }

        // Admin
        if ($user->isAdmin()) {
            return;
        }

        // Roles
        $role = $this->userRepo->getBusinessRole($user, $business);
        if (!$role) {
            throw new UnauthorizedException('You do not have permission to view this business.');
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
            throw new UnauthorizedException('Only the owner or managers can update the business.');
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
            throw new UnauthorizedException('Only the owner can delete the business.');
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
            throw new UnauthorizedException('Only the owner can publish the business.');
        }
    }

    /**
     * Non-role based check for the state of the business itself.
     */
    public function ensureBusinessIsApproved(Business $business): void
    {
        if ($business->state !== BusinessStateEnum::APPROVED) {
            throw new UnauthorizedException('Business must be approved to perform this action.');
        }
    }
}
