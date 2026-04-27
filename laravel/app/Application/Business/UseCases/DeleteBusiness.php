<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;

use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Application\Auth\Services\BusinessAuthorizationService;

class DeleteBusiness
{
    public function __construct(
        private readonly BusinessAuthorizationService $authService,
        private readonly BusinessRepositoryInterface $businessRepo
    ) {}

    /**
     * Executes the delete business use case. It checks if the business exists and if the user has permission to delete it, then deletes the business.
     * @param int $businessId The ID of the business to delete.
     * @param User $user The user performing the delete operation.
     * @return void
     */
    public function execute(int $businessId, User $user): void
    {
        DB::transaction(function () use ($businessId, $user) {
            $business = $this->businessRepo->findForManagement($businessId);
            $this->authService->ensureCanDeleteBusiness($user, $business);
            $this->businessRepo->delete($business);
        });
    }
}
