<?php

namespace App\Policies;

use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Models\Auth\User;
use App\Models\Business\Business;

class BusinessPolicy
{
    public function __construct(private BusinessAuthorizationService $authService) {}

    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function view(User $user, Business $business): bool
    {
        if ($business->is_published) {
            return true;
        }

        if (!$user) {
            return false;
        }

        return $this->runCheck(fn() => $this->authService->ensureCanViewBusiness($user, $business));
    }

    public function update(User $user, Business $business): bool
    {
        return $this->runCheck(fn() => $this->authService->ensureCanUpdateBusiness($user, $business));
    }

    public function delete(User $user, Business $business): bool
    {
        return $this->runCheck(fn() => $this->authService->ensureCanDeleteBusiness($user, $business));
    }

    public function publish(User $user, Business $business): bool
    {
        return $this->runCheck(fn() => $this->authService->ensureCanPublishBusiness($user, $business));
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
