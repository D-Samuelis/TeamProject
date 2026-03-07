<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Application\Auth\Services\BusinessAuthorizationService;

class RestoreBusiness
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private BusinessAuthorizationService $businessAuthService,
        private BusinessRepositoryInterface $businessRepo
    ) {}

    public function execute(int $businessId, int $userId): void
    {
        DB::transaction(function () use ($businessId, $userId) {
            $business = $this->businessRepo->findDeletedById($businessId);
            $user = $this->userRepo->findById($userId);

            $this->businessAuthService->ensureCanUpdateBusiness($user, $business);

            $this->businessRepo->restore($business);
        });
    }
}
