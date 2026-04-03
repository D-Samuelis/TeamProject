<?php

namespace App\Application\Appointment\UseCases;

use App\Application\Appointment\DTO\GetSlotsDTO;
use App\Application\Appointment\Services\SlotGeneratorService;
use App\Application\Asset\UseCases\GetAsset;
use App\Application\Service\UseCases\GetBranchService;

class GetAvailableSlots
{
    public function __construct(
        private readonly SlotGeneratorService $generator,
        private readonly GetAsset $getAsset,
        private readonly GetBranchService $getBranchService,
    ) {}

    /** @return array<string, string[]> */
    public function execute(GetSlotsDTO $dto, $user = null): array
    {
        $asset   = $this->getAsset->execute($dto->assetId, $user);
        $service = $this->getBranchService->execute($dto->serviceId, $user);

        $result = [];
        $cursor = $dto->from->copy()->startOfDay();

        while ($cursor->lte($dto->to)) {
            $result[$cursor->toDateString()] = $this->generator->generate(
                $asset,
                $cursor->copy(),
                $service->effective_duration
            );
            $cursor->addDay();
        }

        return $result;
    }
}
