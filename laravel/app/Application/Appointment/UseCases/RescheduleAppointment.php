<?php

namespace App\Application\Appointment\UseCases;

use App\Application\Auth\Services\AppointmentAuthorizationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Application\Appointment\DTO\RescheduleAppointmentDTO;
use App\Application\Appointment\Services\SlotGeneratorService;
use App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Appointment;

class RescheduleAppointment
{
    public function __construct(
        private readonly AppointmentRepositoryInterface  $appointmentRepo,
        private readonly SlotGeneratorService            $generator,
        private readonly AppointmentAuthorizationService $authService,
    ) {}

    public function execute(RescheduleAppointmentDTO $dto, User $user): Appointment
    {
        $appointment = $this->appointmentRepo->findById($dto->appointmentId);
        abort_if(!$appointment, 404);

        $this->authService->ensureCanUpdateAppointment($user, $appointment);

        if (!$user->isAdmin() && in_array($appointment->status, ['cancelled', 'confirmed'])) {
            throw ValidationException::withMessages([
                'status' => 'Cancelled or confirmed appointments cannot be rescheduled.',
            ]);
        }

        return DB::transaction(function () use ($dto, $appointment) {
            $date    = Carbon::parse($dto->date);
            $startAt = $dto->startAt;

            $available = $this->generator->generate(
                $appointment->asset,
                $date,
                $appointment->service->duration_minutes
            );

            if (!in_array($startAt, $available, true)) {
                throw ValidationException::withMessages([
                    'start_at' => 'This slot is not available. Please choose another time.',
                ]);
            }

            return $this->appointmentRepo->update($appointment, [
                'date'     => $date->toDateString(),
                'start_at' => $startAt . ':00',
            ]);
        });
    }
}
