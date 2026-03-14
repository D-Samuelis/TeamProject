<?php

namespace App\Application\Appointment\DTO;

use App\Http\Requests\Appointment\StoreAppointmentRequest;

class CreateAppointmentDTO
{
    public function __construct(
        public readonly int    $assetId,
        public readonly int    $serviceId,
        public readonly string $date,      // 'Y-m-d'
        public readonly string $startAt,   // 'H:i'
    ) {}

    public static function fromRequest(StoreAppointmentRequest $request): self
    {
        return new self(
            assetId:   $request->validated('asset_id'),
            serviceId: $request->validated('service_id'),
            date:      $request->validated('date'),
            startAt:   $request->validated('start_at'),
        );
    }
}
