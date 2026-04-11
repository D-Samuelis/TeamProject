<?php

namespace App\Application\Appointment\DTO;

use App\Http\Requests\Appointment\UpdateAppointmentRequest;

class UpdateAppointmentDTO
{
    public function __construct(
        public readonly int     $appointmentId,
        public readonly ?string $date     = null,
        public readonly ?string $startAt  = null,
        public readonly ?string $status   = null,
    ) {}

    public static function fromRequest(int $appointmentId, UpdateAppointmentRequest $request): self
    {
        return new self(
            appointmentId: $appointmentId,
            date:          $request->validated('date'),
            startAt:       $request->validated('start_at'),
            status:        $request->validated('status'),
        );
    }
}
