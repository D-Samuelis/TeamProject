<?php

namespace App\Policies;

use App\Application\Auth\Services\ServiceAuthorizationService;
use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Models\Business\Branch;
use App\Models\Business\Service;
use Illuminate\Auth\Access\AuthorizationException;

class ServicePolicy
{
    public function __construct(private readonly ServiceAuthorizationService $authService) {}

    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function view(User $user, Service $service): bool
    {
        return $this->runCheck(fn() => $this->authService->ensureCanViewService($user, $service));
    }

    public function create(User $user, Business $business): bool
    {
        return $this->runCheck(fn() => $this->authService->ensureCanCreateService($user, $business));
    }

    public function update(User $user, Service $service): bool
    {
        return $this->runCheck(fn() => $this->authService->ensureCanUpdateService($user, $service));
    }

    public function delete(User $user, Service $service): bool
    {
        return $this->runCheck(fn() => $this->authService->ensureCanDeleteService($user, $service));
    }

    public function restore(User $user, Service $service): bool
    {
        return $this->runCheck(fn() => $this->authService->ensureCanUpdateService($user, $service));
    }

    public function assign(User $user, Service $service, Branch $branch): bool
    {
        return $this->runCheck(
            fn() => $this->authService->ensureCanAssignServiceToBranch($user, $service->business, $branch)
        );
    }

    private function runCheck(callable $check): bool
    {
        try {
            $check();
            return true;
        } catch (AuthorizationException) {
            return false;
        }
    }
}
