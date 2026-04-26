<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;

use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Application\Auth\Services\BusinessAuthorizationService;

use App\Exceptions\Business\BusinessNotFoundException;

/**
 * Use case for restoring a deleted business. It checks if the user has permission to update the business and then restores it.
 * Throws BusinessNotFoundException if the business does not exist or the user does not have access. Returns void if successful.
 * @param int $businessId The ID of the business to restore.
 * @param User $user The user performing the restore operation.
 * @return void
 * @throws BusinessNotFoundException If the business is not found or the user does not have permission to restore it.
 */
class RestoreBusiness
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

            $this->authService->ensureCanUpdateBusiness($user, $business);
            $this->businessRepo->restore($business);
        });
    }
}
