<?php

namespace App\Application\Business\UseCases;

use App\Models\Auth\User;
use App\Models\Business\Business;

use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Application\Auth\Services\BusinessAuthorizationService;

class GetBusiness
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BusinessAuthorizationService $authService
    ) {}

    /**
     * Executes the get business use case. It checks if the user has permission to view the business and returns it if found.
     * @param int $businessId The ID of the business to retrieve.
     * @param User|null $user The user requesting the business, or null for unauthenticated access.
     * @return Business The retrieved business.
     */
    public function execute(int $businessId, ?User $user = null): Business
    {
        if ($user) {
            $business = $this->businessRepo->findForManagement($businessId);
            $this->authService->ensureCanViewBusiness($user, $business);
            return $business;
        }

        $business = $this->businessRepo->findActive($businessId);
        return $business;
    }
}
