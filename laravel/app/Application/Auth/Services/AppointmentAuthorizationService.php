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
        if ($appointment->user_id === $user->id) return;

        $role = $this->userRepo->getAssetRole($user, $appointment->asset);
        if ($role) return;

        throw new DomainException('You do not have permission to view this appointment.');
    }

    public function ensureCanCreateAppointment(User $user): void
    {
        if ($user->isAdmin()) return;
    }

    public function ensureCanUpdateAppointment(User $user, Appointment $appointment): void
    {
        if ($user->isAdmin()) return;

        if ($appointment->user_id === $user->id) return;

        $businessRole = $this->userRepo->getBusinessRole($user, $appointment->asset->branch->business);
        if ($businessRole && $businessRole->canUpdate()) return;

        $branchRole = $this->userRepo->getBranchRole($user, $appointment->asset->branch);
        if ($branchRole && $branchRole->canUpdate()) return;

        throw new DomainException('You do not have permission to update this appointment.');
    }

    public function ensureCanDeleteAppointment(User $user, Appointment $appointment): void
    {
        if ($user->isAdmin()) return;

        $businessRole = $this->userRepo->getBusinessRole($user, $appointment->asset->branch->business);
        if ($businessRole && $businessRole->canUpdate()) return;

        $branchRole = $this->userRepo->getBranchRole($user, $appointment->asset->branch);
        if ($branchRole && $branchRole->canUpdate()) return;

        throw new DomainException('You do not have permission to delete this appointment.');
    }

    public function ensureCanChangeStatus(User $user, Appointment $appointment, string $status): void
    {
        if ($user->isAdmin()) return;

        $businessRole = $this->userRepo->getBusinessRole($user, $appointment->asset->branch->business);
        if ($businessRole && $businessRole->canUpdate()) return;

        $branchRole = $this->userRepo->getBranchRole($user, $appointment->asset->branch);
        if ($branchRole && $branchRole->canUpdate()) return;

        $currentStatus = $this->appointmentRepo->getCurrentStatus($appointment);

        if ($appointment->user_id === $user->id && $status === 'cancelled') {
            $this->ensureCancellationPeriodAllows($appointment);
            return;
        }
        throw new \DomainException('You do not have permission to set this status.');
    }

    private function ensureCancellationPeriodAllows(Appointment $appointment): void
    {
        $minutes = $appointment->service->cancellation_period_minutes;

        if (is_null($minutes)) return;

        $appointmentDateTime = \Carbon\Carbon::parse($appointment->date->toDateString())
            ->setHour($appointment->start_at->hour)
            ->setMinute($appointment->start_at->minute)
            ->setSecond(0);
        $cancelDeadline = $appointmentDateTime->subMinutes($minutes);

        if (now()->isAfter($cancelDeadline)) {
            throw new \DomainException(
                'This appointment can no longer be cancelled. ' .
                'Cancellations must be made at least ' .
                \App\Application\Service\Services\DurationParser::fromMinutes($minutes) . ' before the appointment.'
            );
        }
    }
}
