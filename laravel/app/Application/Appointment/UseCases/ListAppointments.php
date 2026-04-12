<?php

namespace App\Application\Appointment\UseCases;

use App\Application\DTO\SearchDTO;
use App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface;
use App\Models\Auth\User;

class ListAppointments
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepo,
    ) {}

    public function execute(array $filters = [], ?User $user = null)
    {
        $dto = SearchDTO::fromArray($filters);
        return $this->appointmentRepo->getForCustomer($dto, $user)->getCollection();
    }
}
