<?php

namespace App\Application\Business\UseCases;

use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;

class GetBusiness
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BusinessAuthorizationService $authService
    ) {}

    /**
     * @param int $businessId The ID of the business to retrieve
     * @param User|null $user The authenticated user (required for management mode)
     */
    public function execute(int $businessId, ?User $user = null): Business
    {
        if ($user) {
            $business = $this->businessRepo->findForManagement($businessId);
            $this->authService->ensureCanViewBusiness($user, $business);
            return $business;
        }

        return $this->businessRepo->findActive($businessId);
    }
}
