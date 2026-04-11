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
        if (in_array($businessRole, [
            \App\Domain\Business\Enums\BusinessRoleEnum::OWNER,
            \App\Domain\Business\Enums\BusinessRoleEnum::MANAGER,
        ])) return;

        $branchRole = $this->userRepo->getBranchRole($user, $appointment->asset->branch);
        if ($branchRole === \App\Domain\Branch\Enums\BranchRoleEnum::MANAGER) return;

        throw new DomainException('You do not have permission to update this appointment.');
    }

    public function ensureCanDeleteAppointment(User $user, Appointment $appointment): void
    {
        if ($user->isAdmin()) return;

        $businessRole = $this->userRepo->getBusinessRole($user, $appointment->asset->branch->business);
        if (in_array($businessRole, [
            \App\Domain\Business\Enums\BusinessRoleEnum::OWNER,
            \App\Domain\Business\Enums\BusinessRoleEnum::MANAGER,
        ])) return;

        $branchRole = $this->userRepo->getBranchRole($user, $appointment->asset->branch);
        if ($branchRole === \App\Domain\Branch\Enums\BranchRoleEnum::MANAGER) return;

        throw new DomainException('You do not have permission to update this appointment.');
    }
}
