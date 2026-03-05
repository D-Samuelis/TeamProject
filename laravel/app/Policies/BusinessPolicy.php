<?php

namespace App\Policies;

use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Models\Auth\User;
use App\Models\Business\Business;

class BusinessPolicy
{
    public function __construct(
        private BusinessAuthorizationService $businessAuthService,
    ) {}

    public function before(User $user, $ability, $model = null): ?bool
    {
        // $model may be null for global abilities
        if ($model instanceof Business) {
            try {
                $this->businessAuthService->ensureCanUpdateBusiness($user, $model);
                return true;
            } catch (\DomainException) {
                return false;
            }
        }

        return null; // fallback to normal policy checks
    }

    public function update(User $user, Business $business): bool
    {
        try {
            $this->businessAuthService->ensureCanUpdateBusiness($user, $business);
            return true;
        } catch (\DomainException) {
            return false;
        }
    }

    public function destroy(User $user, Business $business): bool
    {
        try {
            $this->businessAuthService->ensureCanDeleteBusiness($user, $business);
            return true;
        } catch (\DomainException) {
            return false;
        }
    }

    public function publish(User $user, Business $business): bool
    {
        try {
            $this->businessAuthService->ensureCanPublishBusiness($user, $business);
            return true;
        } catch (\DomainException) {
            return false;
        }
    }
}
