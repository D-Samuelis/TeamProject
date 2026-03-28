<?php

namespace App\Application\Auth\Services;

use App\Models\Auth\User;
use App\Models\Business\Service;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use Illuminate\Auth\Access\AuthorizationException;

class ServiceAuthorizationService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepo
    ) {}

    public function ensureCanCreateService(User $user, Business $business): void
    {
        if ($user->isAdmin()) return;

        $role = $this->userRepo->getBusinessRole($user, $business);

        if (!$role || !$role->canUpdate()) {
            throw new AuthorizationException('You do not have permission to create services.');
        }
    }

    public function ensureCanUpdateService(User $user, Service $service): void
    {
        if ($user->isAdmin()) return;

        $businessRole = $this->userRepo->getBusinessRole($user, $service->business);
        if ($businessRole && $businessRole->canUpdate()) return;

        throw new AuthorizationException('You do not have permission to update this service.');
    }

    public function ensureCanDeleteService(User $user, Service $service): void
    {
        if ($user->isAdmin()) return;

        $businessRole = $this->userRepo->getBusinessRole($user, $service->business);
        if ($businessRole && $businessRole->canDelete()) return;

        throw new AuthorizationException('You do not have permission to delete this service.');
    }

    public function ensureCanAssignServiceToBranch(User $user, Business $business, Branch $branch): void
    {
        if ($user->isAdmin()) return;

        $businessRole = $this->userRepo->getBusinessRole($user, $business);
        if ($businessRole && $businessRole->canUpdate()) return;

        $branchRole = $this->userRepo->getBranchRole($user, $branch);
        if ($branchRole && $branchRole->canAssign()) return;

        throw new AuthorizationException('You do not have permission to assign services to this branch.');
    }

    public function ensureCanViewService(User $user, Service $service): void
    {
        if ($user->isAdmin()) return;

        $businessRole = $this->userRepo->getBusinessRole($user, $service->business);
        if ($businessRole) return;

        foreach ($service->business->branches as $branch) {
            $branchRole = $this->userRepo->getBranchRole($user, $branch);
            if ($branchRole) return;
        }

        throw new AuthorizationException('You do not have permission to view this service.');
    }
}
