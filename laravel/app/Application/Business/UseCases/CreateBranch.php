<?php

namespace App\Application\Business\UseCases;

use App\Application\Business\DTO\CreateBranchDTO;
use App\Domain\Business\Entities\Branch;
use App\Domain\Business\Entities\Business;

use App\Domain\Business\Services\BusinessAuthorizationService;

class CreateBranch
{
    public function __construct(
        private BusinessAuthorizationService $authService
    ) {}

    public function execute(CreateBranchDTO $dto, int $userId): void
    {
        $business = Business::findOrFail($dto->business_id);

        $this->authService->ensureOwner($business, $userId);

        Branch::create([
            'business_id' => $dto->business_id,
            'name' => $dto->name,
            'type' => $dto->type,
            'address_line1' => $dto->addressLine1,
            'city' => $dto->city,
            'postal_code' => $dto->postalCode,
            'country' => $dto->country,
        ]);
    }
}
