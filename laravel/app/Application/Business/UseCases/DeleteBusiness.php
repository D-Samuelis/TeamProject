<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;

use App\Application\Auth\Services\AuthorizationService;
use App\Domain\Business\Repositories\BusinessRepositoryInterface;
use App\Domain\Business\Entities\Business as DomainBusiness;

class DeleteBusiness
{
    public function __construct(
        private AuthorizationService $authService,
        private BusinessRepositoryInterface $businessRepo
    ) {}

    public function execute(DomainBusiness $business, int $userId): void
    {
        DB::transaction(function () use ($business, $userId) {

            // Authorization
            $this->authService->ensureCanManageBusiness($business, $userId);

            // Delete
            $this->businessRepo->delete($business->id, $userId);
        });
    }
}
