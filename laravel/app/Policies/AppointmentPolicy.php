<?php

namespace App\Policies;

use App\Application\Auth\Services\AppointmentAuthorizationService;
use App\Models\Auth\User;
use App\Models\Business\Appointment;

class AppointmentPolicy
{
    public function __construct(
        private AppointmentAuthorizationService $appointmentAuthService,
    ) {}

    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $this->runCheck(fn() => $this->appointmentAuthService->ensureCanViewAppointment($user, $appointment));
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $this->runCheck(fn() => $this->appointmentAuthService->ensureCanUpdateAppointment($user, $appointment));
    }

    public function updateStatus(User $user, Appointment $appointment, string $status): bool
    {
        return $this->runCheck(fn() => $this->appointmentAuthService->ensureCanChangeStatus($user, $appointment, $status));
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $this->runCheck(fn() => $this->appointmentAuthService->ensureCanDeleteAppointment($user, $appointment));
    }

    private function runCheck(callable $check): bool
    {
        try {
            $check();
            return true;
        } catch (\DomainException) {
            return false;
        }
    }
}
