<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Application\Auth\Services\BusinessAuthorizationService;

class RestoreBusiness
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepo,
        private readonly BusinessAuthorizationService $authService,
        private readonly BusinessRepositoryInterface $businessRepo
    ) {}

    public function execute(int $businessId, int $userId): void
    {
        DB::transaction(function () use ($businessId, $userId) {
            $user = $this->userRepo->findById($userId);
            $business = $this->businessRepo->findDeletedById($businessId);

            $this->authService->ensureCanUpdateBusiness($user, $business);
            $this->businessRepo->restore($business);
        });
    }
}
