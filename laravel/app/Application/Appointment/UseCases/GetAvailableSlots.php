<?php

namespace App\Application\Appointment\UseCases;

use App\Application\Appointment\DTO\GetSlotsDTO;
use App\Application\Appointment\Services\SlotGeneratorService;
use App\Application\Asset\UseCases\GetAsset;
use App\Application\Service\UseCases\GetService;

class GetAvailableSlots
{
    public function __construct(
        private readonly SlotGeneratorService $generator,
        private readonly GetAsset $getAsset,
        private readonly GetService $getService,
    ) {}

    /** @return array<string, string[]> */
    public function execute(GetSlotsDTO $dto, $user = null): array
    {
        $asset   = $this->getAsset->execute($dto->assetId, $user);
        $service = $this->getService->execute($dto->serviceId, $user);

        $result = [];
        $cursor = $dto->from->copy()->startOfDay();

        while ($cursor->lte($dto->to)) {
            $result[$cursor->toDateString()] = $this->generator->generate(
                $asset,
                $cursor->copy(),
                $service->duration_minutes
            );
            $cursor->addDay();
        }

        return $result;
    }
}
