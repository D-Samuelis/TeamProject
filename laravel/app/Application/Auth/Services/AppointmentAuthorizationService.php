<?php

namespace App\Application\Auth\Services;

use App\Models\Business\Appointment;
use DomainException;
use App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Asset;

class AppointmentAuthorizationService
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepo,
        private UserRepositoryInterface  $userRepo,
    ) {}

    public function ensureCanViewAppointment(User $user, Appointment $appointment): void
    {
        if ($user->isAdmin()) return;

        if (! $appointment->user()->id == $user->id) {
            throw new DomainException('You do not have permission to view this asset.');
        }
    }

    public function ensureCanCreateAppointment(User $user): void
    {
        if ($user->isAdmin()) return;
    }

    public function ensureCanUpdateAppointment(User $user, Appointment $appointment): void
    {
        if ($user->isAdmin()) return;

        throw new DomainException('Not admin');
    }

    public function ensureCanDeleteAppointment(User $user, Appointment $appointment): void
    {
        if ($user->isAdmin()) return;

        throw new DomainException('Not admin');
    }
}
