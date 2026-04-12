<?php

namespace App\Application\Appointment\UseCases;

use Illuminate\Support\Facades\DB;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface;
use App\Application\Auth\Services\AppointmentAuthorizationService;

class DeleteAppointment
{
    public function __construct(
        private UserRepositoryInterface    $userRepo,
        private AppointmentAuthorizationService  $authService,
        private AppointmentRepositoryInterface   $appointmentRepo,
    ) {}

    public function execute(int $appointmentId, int $userId): void
    {
        DB::transaction(function () use ($appointmentId, $userId) {
            $appointment = $this->appointmentRepo->findById($appointmentId);
            abort_if(! $appointment, 404);

            $user = $this->userRepo->findById($userId);
            $this->authService->ensureCanDeleteAppointment($user, $appointment);

            $this->appointmentRepo->delete($appointment);
        });
    }
}
