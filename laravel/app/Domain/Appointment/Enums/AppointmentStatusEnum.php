<?php

namespace App\Domain\Appointment\Enums;

enum AppointmentStatusEnum: string
{
    case PENDING = 'pending';
    case RESERVED = 'reserved';
    case CANCELLED = 'cancelled';
    case SHOW = 'show';
    case NO_SHOW = 'no_show';

    public function canChangeSatatus(): bool
    {
        return $this === self::PENDING;
    }
}
