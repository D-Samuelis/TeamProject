<?php

namespace App\Application\Auth\Services;

use DomainException;
use App\Models\Auth\User;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use App\Domain\Branch\Enums\BranchRoleEnum;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\User\Interfaces\UserRepositoryInterface;

class BranchAuthorizationService
{
    public function __construct(private UserRepositoryInterface $userRepo) {}

    public function ensureCanCreateBranch(User $user, Business $business): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $role = $this->userRepo->getBusinessRole($user, $business);

        if (!in_array($role, [BusinessRoleEnum::OWNER])) {
            throw new DomainException('Only the business owner can create branches.');
        }
    }

    public function ensureCanDeleteBranch(User $user, Branch $branch): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $businessRole = $this->userRepo->getBusinessRole($user, $branch->business);

        if ($businessRole && in_array($businessRole, [BusinessRoleEnum::OWNER])) {
            return;
        }

        $branchRole = $this->userRepo->getBranchRole($user, $branch);

        if ($branchRole && in_array($branchRole, [BranchRoleEnum::MANAGER])) {
            return;
        }

        throw new DomainException('You do not have permission to delete this branch.');
    }

    public function ensureCanUpdateBranch(User $user, Branch $branch): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $businessRole = $this->userRepo->getBusinessRole($user, $branch->business);
        if ($businessRole && $businessRole->canUpdate()) {
            return;
        }

        $branchRole = $this->userRepo->getBranchRole($user, $branch);
        if ($branchRole && in_array($branchRole, [BranchRoleEnum::MANAGER])) {
            return;
        }

        throw new DomainException('Only business owners or branch managers can update this branch.');
    }
}
