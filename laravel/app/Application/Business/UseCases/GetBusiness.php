<?php

namespace App\Application\Business\UseCases;

use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Business;

class GetBusiness
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BusinessAuthorizationService $authService
    ) {}

    public function execute(int $businessId, User $user): Business
    {
        $business = $this->businessRepo->findById($businessId, true);
        $this->authService->ensureCanViewBusiness($user, $business);

        return $business;
    }
}
