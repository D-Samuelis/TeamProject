<?php

namespace App\Repositories\Service;

use Illuminate\Database\Eloquent\Builder;
use App\Application\DTO\SearchDTO;
use App\Domain\Service\Enums\ServiceRoleEnum;
use Illuminate\Support\Collection;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Business\Service;

class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * PUBLIC
     */
    public function findActive(int $id): Service
    {
        return Service::query()->where('is_active', true)->whereHas('business', fn($q) => $q->where('is_published', true))->findOrFail($id);
    }

    public function search(SearchDTO $dto)
    {
        $query = Service::query()
            ->where('is_active', true)
            ->whereHas('business', fn($q) => $q->where('is_published', true));

        $this->applyServiceFilters($query, $dto);

        return $query
            ->with('business')
            ->latest()
            ->paginate($dto->perPage);
    }

    public function findMultipleByIds(array $ids): Collection
    {
        return Service::whereIn('id', $ids)->get();
    }

    /**
     * MANAGEMENT
     */
    public function findForManagement(int $id): Service
    {
        return Service::withTrashed()->findOrFail($id);
    }

    public function findWithinBusiness(int $serviceId, int $businessId): Service
    {
        return Service::where('id', $serviceId)
            ->where('business_id', $businessId)
            ->firstOrFail();
    }

    public function update(Service $service, array $data): Service
    {
        if (isset($data['branch_ids'])) {
            $service->branches()->sync($data['branch_ids']);
            unset($data['branch_ids']);
        }

        $service->update($data);
        return $service;
    }

    public function save(array $data): Service
    {
        $branchIds = $data['branch_ids'] ?? [];
        unset($data['branch_ids']);

        $service = Service::create($data);

        if (!empty($branchIds)) {
            $service->branches()->sync($branchIds);
        }

        return $service;
    }

    public function delete(Service $service): void
    {
        $service->update([
            'is_active' => false,
            'delete_after' => now()->addDays(7),
        ]);
        $service->delete();
    }

    public function restore(Service $service): void
    {
        $service->update([
            'delete_after' => null,
            'is_active' => true,
        ]);

        $service->restore();
    }

    public function attachBranches(Service $service, array $branchIds): void
    {
        $service->branches()->sync($branchIds);
    }

    public function attachUser(Service $service, int $userId, ServiceRoleEnum $role): void
    {
        $service->users()->attach($userId, ['role' => $role->value]);
    }

    public function detachUser($service, $userId): Service
    {
        return $service->users()->detach($userId);
    }

    public function count(SearchDTO $dto): int
    {
        $query = Service::query()
            ->where('is_active', true)
            ->whereHas('business', fn($q) => $q->where('is_published', true));

        $this->applyServiceFilters($query, $dto);

        return $query->count();
    }

    /**
     * PRIVATE HELPERS
     */
    private function applyServiceFilters(Builder $query, SearchDTO $dto): void
    {
        if ($dto->query) {
            $keyword = $dto->query;
            $query->where(function ($sub) use ($keyword) {
                $sub->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas(
                        'business',
                        fn($b) =>
                        $b->where('name', 'like', "%{$keyword}%")
                    )
                    ->orWhereHas(
                        'branches',
                        fn($br) =>
                        $br->where('city', 'like', "%{$keyword}%")
                    );
            });
        }

        if ($dto->city) {
            $query->whereHas('branches', fn($q) => $q->where('city', $dto->city));
        }

        if ($dto->maxPrice) {
            $query->where('price', '<=', $dto->maxPrice);
        }

        if ($dto->maxDuration) {
            $query->where('duration_minutes', '<=', $dto->maxDuration);
        }

        if (!empty($dto->locationTypes)) {
            $query->whereIn('location_type', $dto->locationTypes);
        }

        if ($dto->businessId) {
            $query->where('business_id', $dto->businessId);
        }
    }
}
