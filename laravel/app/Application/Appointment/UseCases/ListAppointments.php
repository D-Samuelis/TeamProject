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
        return $this->appointmentRepo->search($dto, $user);
    }
}
