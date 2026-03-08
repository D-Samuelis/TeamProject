<?php

namespace App\Policies;

use App\Application\Auth\Services\BranchAuthorizationService;
use App\Models\Auth\User;
use App\Models\Business\Branch;
use App\Models\Business\Business;

class BranchPolicy
{
    public function __construct(private BranchAuthorizationService $authService) {}

    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Branch $branch): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Business $business): bool
    {
        return $this->runCheck(fn() => $this->authService->ensureCanCreateBranch($user, $business));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Branch $branch): bool
    {
        return $this->runCheck(fn() => $this->authService->ensureCanUpdateBranch($user, $branch));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Branch $branch): bool
    {
        return $this->runCheck(fn() => $this->authService->ensureCanDeleteBranch($user, $branch));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Branch $branch): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Branch $branch): bool
    {
        return false;
    }

    private function runCheck(callable $check): bool
    {
        try {
            $check();

            return true;
        } catch (\DomainException) {
            return false;
        }
    }
}
