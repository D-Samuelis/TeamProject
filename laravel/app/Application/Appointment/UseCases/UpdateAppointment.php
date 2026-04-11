<?php

namespace App\Application\Appointment\UseCases;

use App\Application\Auth\Services\AppointmentAuthorizationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Application\Appointment\DTO\UpdateAppointmentDTO;
use App\Application\Appointment\Services\SlotGeneratorService;
use App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Appointment;

class UpdateAppointment
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepo,
        private readonly AppointmentAuthorizationService $authService,
    ) {}

    public function execute(UpdateAppointmentDTO $dto, User $user): Appointment
    {
        $appointment = $this->appointmentRepo->findById($dto->appointmentId);
        abort_if(!$appointment, 404);

        $this->authService->ensureCanUpdateAppointment($user, $appointment);

        if ($dto->status) {
            $this->authService->ensureCanChangeStatus($user, $appointment, $dto->status);
        }

        return $this->appointmentRepo->update($appointment, array_filter([
            'status' => $dto->status,
        ], fn($v) => !is_null($v)));
    }
}
