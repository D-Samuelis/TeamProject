<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Models\Auth\User;

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

            $this->authService->ensureCanUpdateBusiness($user, $business);

            $this->businessRepo->restore($business);
        });
    }
}
