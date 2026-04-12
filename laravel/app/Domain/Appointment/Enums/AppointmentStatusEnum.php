<?php

namespace App\Domain\Appointment\Enums;

enum AppointmentStatusEnum: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';

    public function canChangeSatatus(): bool
    {
        return $this === self::PENDING;
    }
}
