<?php

namespace App\Policies;

use App\Application\Auth\AuthorizationService;
use App\Models\Auth\User;
use App\Domain\Business\Entities\Business;

class BusinessPolicy
{
    public function __construct(private AuthorizationService $authService) {}

    // Can user create a branch for this business?
    public function createBranch(User $user, int $businessId): bool
    {
        try {
            $this->authService->ensureCanCreateBranch($businessId, $user->id);
            return true;
        } catch (\DomainException) {
            return false;
        }
    }

    // Can user manage this business (update, create service, etc.)
    public function manage(User $user, Business $business): bool
    {
        try {
            $this->authService->ensureCanManageBusiness($business->id, $user->id);
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
}