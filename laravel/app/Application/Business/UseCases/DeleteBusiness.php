<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Business\Repositories\BusinessRepositoryInterface;
use App\Application\Auth\Services\BusinessAuthorizationService;

class DeleteBusiness
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private BusinessAuthorizationService $businessAuthService,
        private BusinessRepositoryInterface $businessRepo
    ) {}

    public function execute(int $businessId, int $userId): void
    {
        DB::transaction(function () use ($businessId, $userId) {
            $business = $this->businessRepo->findById($businessId);
            if (!$business) {
                throw new \DomainException('Business not found.');
            }
            $user = $this->userRepo->findById($userId);
            if (!$user) {
                throw new \DomainException('User not found.');
            }

            // Authorization
            $this->businessAuthService->ensureCanDeleteBusiness($user, $business);

            // Delete
            $this->businessRepo->delete($business);
        });
    }
}
