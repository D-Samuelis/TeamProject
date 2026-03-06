<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Models\Business\Business;

class RestoreBusiness
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private BusinessAuthorizationService $businessAuthService,
        private BusinessRepositoryInterface $businessRepo
    ) {}

    public function execute(Business $business, int $userId): void
    {
        DB::transaction(function () use ($business, $userId) {
            if (!$business) {
                throw new \DomainException('Business not found.');
            }

            $user = $this->userRepo->findById($userId);
            if (!$user) {
                throw new \DomainException('User not found.');
            }

            // $this->businessAuthService->ensureCanUpdateBusiness($user, $business);

            $this->businessRepo->restore($business);
        });
    }
}
