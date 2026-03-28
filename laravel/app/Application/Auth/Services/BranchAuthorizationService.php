<?php

namespace App\Application\Auth\Services;

use App\Models\Auth\User;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class BranchAuthorizationService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepo
    ) {}

    public function ensureCanCreateBranch(User $user, Business $business): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $role = $this->userRepo->getBusinessRole($user, $business);

        if (!$role || !$role->canUpdate()) {
            throw new AuthorizationException('You do not have permission to create branches.');
        }
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

        throw new AuthorizationException('You do not have permission to update this branch.');
    }

    public function ensureCanDeleteBranch(User $user, Branch $branch): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $businessRole = $this->userRepo->getBusinessRole($user, $branch->business);
        if ($businessRole && $businessRole->canUpdate()) {
            return;
        }

        throw new AuthorizationException('You do not have permission to delete this branch.');
    }

    public function ensureCanManageBranchStaff(User $user, Branch $branch): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $businessRole = $this->userRepo->getBusinessRole($user, $branch->business);
        if ($businessRole && $businessRole->canManageStaff()) {
            return;
        }

        $branchRole = $this->userRepo->getBranchRole($user, $branch);
        if ($branchRole && $branchRole->canManageStaff()) {
            return;
        }

        throw new AuthorizationException('You are not authorized to manage staff for this branch.');
    }

    /**
     * Check if a user can view branch details.
     */
    public function ensureCanViewBranch(?User $user, Branch $branch): void
    {
        if ($branch->is_active) return;

        if (!$user) {
            throw new AuthorizationException('This branch is private.');
        }

        if ($user->isAdmin()) return;

        $businessRole = $this->userRepo->getBusinessRole($user, $branch->business);
        if ($businessRole) return;

        $branchRole = $this->userRepo->getBranchRole($user, $branch);
        if ($branchRole) return;

        throw new AuthorizationException('You do not have permission to view this branch.');
    }
}
