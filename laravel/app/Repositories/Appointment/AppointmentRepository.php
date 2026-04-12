<?php

namespace App\Repositories\Appointment;

use App\Application\DTO\SearchDTO;
use App\Domain\Appointment\Enums\AppointmentStatusEnum;
use App\Models\Auth\User;
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

    public function getForCustomer(SearchDTO $dto, ?User $user = null)
    {
        $query = Appointment::query()->with(['user', 'service', 'asset.branch.business']);

        // 1. If not an Admin, strictly limit to the user's own bookings
        if ($user && !$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        // 2. Apply Optional Filters (from SearchDTO)
        if (!empty($dto->filters['status'])) {
            $query->where('status', $dto->filters['status']);
        }

        if (!empty($dto->filters['date'])) {
            $query->whereDate('date', $dto->filters['date']);
        }

        return $query->latest('start_at')->paginate($dto->perPage);
    }

    public function search(SearchDTO $dto, ?User $user = null)
    {
        $query = Appointment::query()->with(['user', 'service', 'asset.branch']);

        if ($user && !$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                // 1. Always show appointments where the user is the customer
                $q->where('user_id', $user->id)

                    // 2. OR show appointments in branches where the user has a role (Staff/Manager)
                    ->orWhereHas('asset.branch', function ($b) use ($user) {
                        $b->whereHas('users', fn($u) => $u->where('users.id', $user->id));
                    })

                    // 3. OR show appointments for services the user is assigned to
                    ->orWhereHas('service', function ($s) use ($user) {
                        $s->whereHas('users', fn($u) => $u->where('users.id', $user->id));
                    })

                    // 4. OR show appointments in businesses the user manages/owns
                    ->orWhereHas('asset.branch.business', function ($biz) use ($user) {
                        $biz->whereHas('users', function ($u) use ($user) {
                            $u->where('users.id', $user->id)
                                ->whereIn('model_has_users.role', ['owner', 'manager']);
                        });
                    });
            });
        }

        // Apply filters from SearchDTO
        if (!empty($dto->filters['status'])) {
            $query->where('status', $dto->filters['status']);
        }

        if (!empty($dto->filters['date'])) {
            $query->whereDate('date', $dto->filters['date']);
        }

        return $query->latest('start_at')->get();
    }

    public function update(Appointment $appointment, array $data): Appointment
    {
        $appointment->update($data);
        return $appointment->fresh();
    }

    public function delete(Appointment $appointment): void
    {
        $appointment->delete();
    }

    public function getCurrentStatus(Appointment $appointment): AppointmentStatusEnum
    {
        return AppointmentStatusEnum::tryFrom($appointment->status);
    }
}
