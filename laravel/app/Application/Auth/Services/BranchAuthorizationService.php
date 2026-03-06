<?php

namespace App\Application\Auth\Services;

use DomainException;

use App\Domain\User\Entities\User as DomainUser;
use App\Domain\Business\Entities\Business as DomainBusiness;
use App\Domain\Business\Entities\Branch as DomainBranch;
use App\Domain\Business\Enums\BranchRoleEnum;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\User\Repositories\UserRepositoryInterface;

class BranchAuthorizationService
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
    ) {}

    public function ensureCanCreateBranch(DomainUser $user, DomainBusiness $business): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $role = $this->userRepo->getBusinessRole($user, $business);

        if (! in_array($role, [BusinessRoleEnum::OWNER])) {
            throw new DomainException('Only the business owner can create branches.');
        }
    }

    public function ensureCanDeleteBranch(DomainUser $user, DomainBranch $branch): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $role = $this->userRepo->getBranchRole($user, $branch);

        if (! in_array($role, [BranchRoleEnum::MANAGER])) {
            throw new DomainException('Only the owner or managers can delete branches.');
        }
    }

    public function ensureCanUpdateBranch(DomainUser $user, DomainBranch $branch): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $role = $this->userRepo->getBranchRole($user, $branch);

        if (! in_array($role, [BranchRoleEnum::MANAGER])) {
            throw new DomainException('Only the owner or managers can update branches.');
        }
    }
}
