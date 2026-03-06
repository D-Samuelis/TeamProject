<?php

namespace App\Application\Auth\Services;

use DomainException;

use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Enums\BusinessStateEnum;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;

use App\Models\Auth\User;
use App\Models\Business\Business;

class BusinessAuthorizationService
{
    public function __construct(
        private BusinessRepositoryInterface $businessRepo,
        private UserRepositoryInterface $userRepo,
    ) {}

    // Remove later? If we want to allow users to create multiple businesses ... .
    public function ensureCanCreateBusiness(User $user): void
    {
        if ($user->is_admin) {
            return;
        }

        $ownsBusiness = $this->businessRepo->existsOwner($user->id);

        if ($ownsBusiness) {
            throw new DomainException('You already own a business and cannot create another one.');
        }
    }

    public function ensureCanDeleteBusiness(User $user, Business $business): void
    {
        if ($user->is_admin) {
            return;
        }

        $role = $this->userRepo->getBusinessRole($user, $business);

        if (! in_array($role, [BusinessRoleEnum::OWNER])) {
            throw new DomainException('Only the owner can delete the business.');
        }
    }

    public function ensureCanUpdateBusiness(User $user, Business $business): void
    {
        if ($user->is_admin) {
            return;
        }

        $role = $this->userRepo->getBusinessRole($user, $business);

        if (! in_array($role, [BusinessRoleEnum::OWNER])) {
            throw new DomainException('Only the owner or managers can update the business.');
        }
    }

    public function ensureCanPublishBusiness(User $user, Business $business): void
    {
        if ($user->is_admin) {
            return;
        }

        $role = $this->userRepo->getBusinessRole($user, $business);

        if (! in_array($role, [BusinessRoleEnum::OWNER, BusinessRoleEnum::MANAGER])) {
            throw new DomainException('Only the owner or managers can publish the business.');
        }
    }

    public function ensureBusinessIsApproved(Business $business): void
    {
        if ($business->state !== BusinessStateEnum::APPROVED) {
            throw new DomainException('Business must be approved to perform this action.');
        }
    }
}