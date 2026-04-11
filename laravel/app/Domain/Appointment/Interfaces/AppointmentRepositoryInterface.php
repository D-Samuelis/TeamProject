<?php

namespace App\Domain\Appointment\Interfaces;

use App\Application\DTO\SearchDTO;
use App\Models\Auth\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\Business\Appointment;

interface AppointmentRepositoryInterface
{
    /**
     * Find booked (non-cancelled) start times for an asset on a given date.
     * Returns strings in 'H:i' format.
     */
    public function getTakenSlots(int $assetId, Carbon $date): Collection;

    /**
     * Persist a new appointment.
     */
    public function save(array $data): Appointment;

    /**
     * Find a single appointment by ID.
     */
    public function findById(int $id): ?Appointment;

    public function getForCustomer(SearchDTO $dto, ?User $user = null): Collection;

    public function search(SearchDTO $dto, ?User $user = null): Collection;

    public function update(Appointment $appointment, array $data): Appointment;

    public function delete(Appointment $appointment): void;
}
