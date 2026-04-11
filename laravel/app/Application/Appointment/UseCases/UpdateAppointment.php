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
        private readonly SlotGeneratorService           $generator,
        private readonly AppointmentAuthorizationService $authService,
    ) {}

    public function execute(UpdateAppointmentDTO $dto, User $user): Appointment
    {
        $appointment = $this->appointmentRepo->findById($dto->appointmentId);
        abort_if(!$appointment, 404);

        $this->authService->ensureCanUpdateAppointment($user, $appointment);

        return DB::transaction(function () use ($dto, $appointment, $user) {
            $data = [];

            if ($dto->date || $dto->startAt) {
                $date    = Carbon::parse($dto->date ?? $appointment->date);
                $startAt = $dto->startAt ?? substr($appointment->start_at, 0, 5);

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

                $data['date']     = $date->toDateString();
                $data['start_at'] = $startAt . ':00';
            }

            if ($dto->status) {
                $isOwner = $appointment->asset->branch->business->users()
                    ->where('user_id', $user->id)
                    ->whereIn('model_has_users.role', ['owner', 'manager'])
                    ->exists();

                if (!$isOwner && $dto->status !== 'cancelled') {
                    throw ValidationException::withMessages([
                        'status' => 'You can only cancel your appointment.',
                    ]);
                }

                $data['status'] = $dto->status;
            }

            return $this->appointmentRepo->update($appointment, $data);
        });
    }

    private function authorize(User $user, Appointment $appointment): void
    {
        $isOwner = $appointment->user_id === $user->id;
        $isManager = $appointment->asset->branch->business->users()
            ->where('user_id', $user->id)
            ->whereIn('model_has_users.role', ['owner', 'manager'])
            ->exists();

        if (!$isOwner && !$isManager) {
            abort(403);
        }
    }
}
