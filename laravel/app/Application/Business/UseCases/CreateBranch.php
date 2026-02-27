<?php

namespace App\Application\Business\UseCases;

use App\Domain\Business\Entities\Branch;
use App\Domain\Business\Entities\Business;

use App\Domain\Business\Services\BusinessAuthorizationService;

class CreateBranch
{
    public function __construct(
        private BusinessAuthorizationService $authService
    ) {}

    public function execute(array $data, int $userId): void
    {
        $business = Business::findOrFail($data['business_id']);

        $this->authService->ensureOwner($business, $userId);

        Branch::create($data);
    }
}
