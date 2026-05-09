<?php

namespace App\Application\Appointment\UseCases;

use App\Application\DTO\AppointmentSearchDTO;
use App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface;
use App\Models\Auth\User;

class ListAppointments
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepo,
    ) {}

    public function execute(AppointmentSearchDTO $dto, ?User $user = null)
    {
        // Non-admins can never filter by another user_id — strip it silently
        if ($user && !$user->isAdmin()) {
            $dto = new AppointmentSearchDTO(
                dateFrom:    $dto->dateFrom,
                dateTo:      $dto->dateTo,
                timeFrom:    $dto->timeFrom,
                timeTo:      $dto->timeTo,
                statuses:    $dto->statuses,
                serviceName: $dto->serviceName,
                priceMin:    $dto->priceMin,
                priceMax:    $dto->priceMax,
                durationMin: $dto->durationMin,
                durationMax: $dto->durationMax,
                userId:      null, // force-cleared
                perPage:     $dto->perPage,
                page:        $dto->page,
            );
        }

        return $this->appointmentRepo->search($dto, $user);
    }
}
