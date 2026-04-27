<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use App\Models\Business\Business;

use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Application\Auth\Services\BusinessAuthorizationService;

class RestoreBusiness
{
    public function __construct(
        private readonly BusinessAuthorizationService $authService,
        private readonly BusinessRepositoryInterface $businessRepo
    ) {}

    /**
     * Executes the restore business use case. It checks if the user has permission to update the business and then restores it.
     * @param int $businessId The ID of the business to restore.
     * @param User $user The user performing the restore operation.
     * @return Business The restored business instance.
     */
    public function execute(int $businessId, User $user): Business
    {
        return DB::transaction(function () use ($businessId, $user) {
            $business = $this->businessRepo->findForManagement($businessId);
            $this->authService->ensureCanUpdateBusiness($user, $business);
            return $this->businessRepo->restore($business);
        });
    }
}
