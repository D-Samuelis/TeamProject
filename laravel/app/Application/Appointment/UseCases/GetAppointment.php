<?php

namespace App\Application\Appointment\UseCases;

use App\Application\Auth\Services\AppointmentAuthorizationService;
use App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Appointment;

class GetAppointment
{
    public function __construct(
        private readonly AppointmentRepositoryInterface  $appointmentRepo,
        private readonly AppointmentAuthorizationService $authService,
    ) {}

    public function execute(int $assetId, ?User $user = null): Appointment
    {
        $appointment = $this->appointmentRepo->findById($assetId);
        abort_if(! $appointment, 404);

        if ($user) {
            $this->authService->ensureCanViewAppointment($user, $appointment);
        }

        return $appointment;
    }
}
