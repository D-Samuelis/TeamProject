<?php

namespace App\Repositories\Appointment;

use App\Application\DTO\AppointmentSearchDTO;
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

    public function assocSearch(AppointmentSearchDTO $dto, ?User $user = null)
    {
        $query = Appointment::query()
            ->with(['user', 'service', 'asset.branch.business']);

        if ($user) {
            $query->where('user_id', $user->id);
        }

        if ($dto->dateFrom) {
            $query->whereDate('date', '>=', $dto->dateFrom);
        }
        if ($dto->dateTo) {
            $query->whereDate('date', '<=', $dto->dateTo);
        }

        if ($dto->timeFrom) {
            $query->whereTime('start_at', '>=', $dto->timeFrom);
        }
        if ($dto->timeTo) {
            $query->whereTime('start_at', '<=', $dto->timeTo);
        }

        if (!empty($dto->statuses)) {
            $query->whereIn('status', $dto->statuses);
        }

        if ($dto->durationMin !== null) {
            $query->where('duration', '>=', $dto->durationMin);
        }
        if ($dto->durationMax !== null) {
            $query->where('duration', '<=', $dto->durationMax);
        }

        $needsServiceJoin = $dto->serviceName || $dto->priceMin !== null || $dto->priceMax !== null;

        if ($needsServiceJoin) {
            $query->whereHas('service', function ($q) use ($dto) {
                if ($dto->serviceName) {
                    $q->where('name', 'like', '%' . $dto->serviceName . '%');
                }
                if ($dto->priceMin !== null) {
                    $q->where('price', '>=', $dto->priceMin);
                }
                if ($dto->priceMax !== null) {
                    $q->where('price', '<=', $dto->priceMax);
                }
            });
        }

        return $query
            ->orderBy('date', 'desc')
            ->orderBy('start_at', 'desc')
            ->paginate($dto->perPage, ['*'], 'page', $dto->page);
    }

    public function search(AppointmentSearchDTO $dto, ?User $user = null)
    {
        $query = Appointment::query()
            ->with(['user', 'service', 'asset.branch.business']);

        if ($user && !$user->isAdmin()) {
            // Resolve all service IDs this user is associated to
            $businessIds = $user->businesses()->pluck('businesses.id');
            $branchIds   = $user->branches()->pluck('branches.id');
            $serviceIds  = $user->services()->pluck('services.id');

            // Expand businesses → branches → services
            $allBranchIds = \App\Models\Business\Branch::whereIn('business_id', $businessIds)
                ->pluck('id')
                ->merge($branchIds)
                ->unique();

            $allServiceIds = \App\Models\Business\Service::whereIn('branch_id', $allBranchIds)
                ->pluck('id')
                ->merge($serviceIds)
                ->unique();

            // Scope to appointments under those services
            $query->whereHas('service', fn ($q) =>
            $q->whereIn('id', $allServiceIds)
            );

            // Optional user filter within their visible scope
            if ($dto->userId) {
                $query->where('user_id', $dto->userId);
            }
        }

        // Admin: optional user filter with no scope restriction
        if ($user?->isAdmin() && $dto->userId) {
            $query->where('user_id', $dto->userId);
        }

        if ($dto->dateFrom) {
            $query->whereDate('date', '>=', $dto->dateFrom);
        }
        if ($dto->dateTo) {
            $query->whereDate('date', '<=', $dto->dateTo);
        }
        if ($dto->timeFrom) {
            $query->whereTime('start_at', '>=', $dto->timeFrom);
        }
        if ($dto->timeTo) {
            $query->whereTime('start_at', '<=', $dto->timeTo);
        }
        if (!empty($dto->statuses)) {
            $query->whereIn('status', $dto->statuses);
        }
        if ($dto->durationMin !== null) {
            $query->where('duration', '>=', $dto->durationMin);
        }
        if ($dto->durationMax !== null) {
            $query->where('duration', '<=', $dto->durationMax);
        }

        $needsServiceJoin = $dto->serviceName || $dto->priceMin !== null || $dto->priceMax !== null;

        if ($needsServiceJoin) {
            $query->whereHas('service', function ($q) use ($dto) {
                if ($dto->serviceName) {
                    $q->where('name', 'like', '%' . $dto->serviceName . '%');
                }
                if ($dto->priceMin !== null) {
                    $q->where('price', '>=', $dto->priceMin);
                }
                if ($dto->priceMax !== null) {
                    $q->where('price', '<=', $dto->priceMax);
                }
            });
        }

        return $query
            ->orderBy('date', 'desc')
            ->orderBy('start_at', 'desc')
            ->paginate($dto->perPage, ['*'], 'page', $dto->page);
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
