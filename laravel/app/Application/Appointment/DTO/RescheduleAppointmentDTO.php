<?php

namespace App\Application\Appointment\DTO;

class RescheduleAppointmentDTO
{
    public function __construct(
        public readonly int    $appointmentId,
        public readonly string $date,
        public readonly string $startAt,
    ) {}
}
