<?php

namespace App\Policies;

use App\Application\Auth\Services\AuthorizationService;
use App\Models\Auth\User;
use App\Domain\Business\Entities\Business as DomainBusiness;

class BusinessPolicy
{
    public function __construct(private AuthorizationService $authService) {}

    // Can user create a branch for this business?
    public function createBranch(User $user, DomainBusiness $business): bool
    {
        try {
            $this->authService->ensureCanCreateBranch($business, $user->id);
            return true;
        } catch (\DomainException) {
            return false;
        }
    }

    // Can user manage this business (update, create service, etc.)
    public function manage(User $user, DomainBusiness $business): bool
    {
        try {
            $this->authService->ensureCanManageBusiness($business, $user->id);
            return true;
        } catch (\DomainException) {
            return false;
        }
    }

    // Can user create a business?
    public function create(User $user): bool
    {
        try {
            $this->authService->ensureCanCreateBusiness($user->id);
            return true;
        } catch (\DomainException) {
            return false;
        }
    }

    public function delete(User $user, DomainBusiness $business): bool
    {
        try {
            $this->authService->ensureCanDeleteBusiness($business, $user->id);
            return true;
        } catch (\DomainException) {
            return false;
        }
    }
}
