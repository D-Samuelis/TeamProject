<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;

use App\Application\Business\DTO\CreateServiceDTO;

use App\Domain\Business\Entities\Branch;
use App\Domain\Business\Entities\Business;
use App\Domain\Business\Entities\Service;

use App\Domain\Business\Services\BusinessAuthorizationService;

class CreateService
{
    public function __construct(private BusinessAuthorizationService $authService) {}

    public function execute(CreateServiceDTO $dto, int $userId): void
    {
        DB::transaction(function () use ($dto, $userId) {
            $business = Business::findOrFail($dto->businessId);

            $this->authService->ensureOwner($business, $userId);

            $service = Service::create([
                'business_id' => $business->id,
                'name' => $dto->name,
                'description' => $dto->description,
                'duration_minutes' => $dto->durationMinutes,
                'price' => $dto->price,
                'location_type' => $dto->locationType,
                'is_active' => $dto->isActive,
            ]);

            if (!empty($dto->branchIds)) {
                $validBranchIds = Branch::where('business_id', $business->id)->whereIn('id', $dto->branchIds)->pluck('id')->toArray();

                $service->branches()->attach($validBranchIds);
            }
        });
    }
}
