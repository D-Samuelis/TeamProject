<?php

namespace App\Repositories\Appointment;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface;
use App\Models\Business\Appointment;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function getTakenSlots(int $assetId, Carbon $date): Collection
    {
        return Appointment::query()
            ->where('asset_id', $assetId)
            ->whereDate('date', $date->toDateString())
            ->whereNotIn('status', ['cancelled'])
            ->pluck('start_at')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'));
    }

    public function save(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function findById(int $id): ?Appointment
    {
        return Appointment::find($id);
    }
}
