<?php

namespace App\Application\Appointment\DTO;

use Carbon\Carbon;
use App\Http\Requests\Appointment\GetSlotsRequest;

class GetSlotsDTO
{
    public function __construct(
        public readonly int    $assetId,
        public readonly int    $serviceId,
        public readonly Carbon $from,
        public readonly Carbon $to,
    ) {}

    public static function fromRequest(GetSlotsRequest $request): self
    {
        return new self(
            assetId:   $request->validated('asset_id'),
            serviceId: $request->validated('service_id'),
            from:      Carbon::parse($request->validated('from')),
            to:        Carbon::parse($request->validated('to')),
        );
    }
}
