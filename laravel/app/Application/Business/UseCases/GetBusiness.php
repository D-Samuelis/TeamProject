<?php

namespace App\Application\Business\UseCases;

use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;

use App\Exceptions\Business\BusinessNotFoundException;

/**
 * Use case for retrieving a business by ID. If a user is provided, it checks if the user has permission to view the business.
 * If no user is provided, it only retrieves active businesses. Throws BusinessNotFoundException if the business does not exist or the user does not have access.
 * Returns the Business model instance if found and accessible.
 * @param int $businessId The ID of the business to retrieve.
 * @param User|null $user The user requesting the business, or null for unauthenticated access.
 * @return Business The retrieved business.
 * @throws BusinessNotFoundException If the business is not found or the user does not have permission to view it.
 */
class GetBusiness
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BusinessAuthorizationService $authService
    ) {}

    public function execute(int $businessId, ?User $user = null): Business
    {
        if ($user) {
            $business = $this->businessRepo->findForManagement($businessId);

            if (!$business) {
                throw new BusinessNotFoundException($businessId);
            }

            $this->authService->ensureCanViewBusiness($user, $business);
            return $business;
        }

        $business = $this->businessRepo->findActive($businessId);

        if (!$business) {
            throw new BusinessNotFoundException($businessId);
        }

        return $business;
    }
}
