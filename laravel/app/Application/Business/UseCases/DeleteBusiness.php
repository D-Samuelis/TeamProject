<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;

use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Application\Auth\Services\BusinessAuthorizationService;

use App\Exceptions\Business\BusinessNotFoundException;

/**
 * Use case for deleting a business. It checks if the user has permission to delete the business and then deletes it.
 * Throws BusinessNotFoundException if the business does not exist or the user does not have access. Returns void if successful.
 * @param int $businessId The ID of the business to delete.
 * @param User $user The user performing the delete operation.
 * @return void
 * @throws BusinessNotFoundException If the business is not found or the user does not have permission to delete it.
 */
class DeleteBusiness
{
    public function __construct(
        private readonly BusinessAuthorizationService $authService,
        private readonly BusinessRepositoryInterface $businessRepo
    ) {}

    public function execute(int $businessId, User $user): void
    {
        DB::transaction(function () use ($businessId, $user) {
            $business = $this->businessRepo->findForManagement($businessId);

            if (!$business) {
                throw new BusinessNotFoundException($businessId);
            }

            $this->authService->ensureCanDeleteBusiness($user, $business);
            $this->businessRepo->delete($business);
        });
    }
}
