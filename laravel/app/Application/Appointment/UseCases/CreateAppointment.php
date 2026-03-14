<?php

namespace App\Application\Appointment\UseCases;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Application\Appointment\DTO\CreateAppointmentDTO;
use App\Application\Appointment\Services\SlotGeneratorService;
use App\Application\Asset\UseCases\GetAsset;
use App\Application\Service\UseCases\GetService;
use App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface;
use App\Models\Business\Appointment;

class CreateAppointment
{
    public function __construct(
        private readonly SlotGeneratorService $generator,
        private readonly GetAsset $getAsset,
        private readonly GetService $getService,
        private readonly AppointmentRepositoryInterface $appointmentRepo,
    ) {}

    /** @throws ValidationException */
    public function execute(CreateAppointmentDTO $dto, int $userId, $user = null): Appointment
    {
        $asset   = $this->getAsset->execute($dto->assetId, $user);
        $service = $this->getService->execute($dto->serviceId, $user);
        $date    = Carbon::parse($dto->date);

        return DB::transaction(function () use ($asset, $service, $userId, $date, $dto) {
            // Re-check inside transaction to prevent double booking
            $available = $this->generator->generate($asset, $date, $service->duration_minutes);

            if (! in_array($dto->startAt, $available, true)) {
                throw ValidationException::withMessages([
                    'start_at' => 'This slot is no longer available. Please choose another time.',
                ]);
            }

            return $this->appointmentRepo->save([
                'user_id'    => $userId,
                'service_id' => $service->id,
                'asset_id'   => $asset->id,
                'status'     => 'pending',
                'duration'   => $service->duration_minutes,
                'date'       => $date->toDateString(),
                'start_at'   => $dto->startAt . ':00',
            ]);
        });
    }
}
