<?php

namespace App\Repositories\Service;

use Illuminate\Database\Eloquent\Builder;
use App\Application\DTO\SearchDTO;
use Illuminate\Support\Collection;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Models\Business\Service;

class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * PUBLIC
     * Note: public-facing service search (end-user marketplace) should use
     * BranchServiceRepository::search() instead, as it searches instances
     * with effective prices and branch context.
     * This findActive/search is for template-level lookups only.
     */
    public function findActive(int $id): Service
    {
        return Service::query()
            ->where('is_active', true)
            ->whereHas('business', fn($q) => $q->where('is_published', true))
            ->findOrFail($id);
    }

    public function search(SearchDTO $dto)
    {
        $query = Service::query()
            ->where('is_active', true)
            ->whereHas('business', fn($q) => $q->where('is_published', true));

        $this->applyServiceFilters($query, $dto);

        return $query
            ->with(['business', 'branchServices'])
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
    public function listForUser(User $user, ?Business $business = null, string $scope = 'active'): Collection
    {
        $query = Service::query();

        if (! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('business.users', fn($q) => $q->where('user_id', $user->id))
                  ->orWhereHas('business.branches.users', fn($q) => $q->where('user_id', $user->id));
            });
        }

        if ($business) {
            $query->where('business_id', $business->id);
        }

        match ($scope) {
            'deleted' => $query->onlyTrashed(),
            'all'     => $query->withTrashed(),
            default   => $query,
        };

        return $query
            ->with(['business', 'branchServices.branch'])
            ->latest()
            ->get();
    }

    public function findForManagement(int $id): Service
    {
        return Service::withTrashed()
            ->with(['business', 'branchServices.branch'])
            ->findOrFail($id);
    }

    public function findWithinBusiness(int $serviceId, int $businessId): Service
    {
        return Service::where('id', $serviceId)
            ->where('business_id', $businessId)
            ->firstOrFail();
    }

    /**
     * DATA PERSISTENCE
     * Service::save/update only manages the template itself.
     * Branch instance creation is handled by BranchServiceRepository.
     */
    public function save(array $data): Service
    {
        return Service::create($data);
    }

    public function update(Service $service, array $data): Service
    {
        $service->update($data);
        return $service;
    }

    public function delete(Service $service): void
    {
        $service->update([
            'is_active'    => false,
            'delete_after' => now()->addDays(7),
        ]);
        $service->delete();
    }

    public function restore(Service $service): void
    {
        $service->update([
            'delete_after' => null,
            'is_active'    => true,
        ]);
        $service->restore();
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
                    ->orWhereHas('business', fn($b) => $b->where('name', 'like', "%{$keyword}%"));
            });
        }

        if ($dto->maxPrice) {
            $query->where('base_price', '<=', $dto->maxPrice);
        }

        if ($dto->maxDuration) {
            $query->where('base_duration_minutes', '<=', $dto->maxDuration);
        }

        if (! empty($dto->locationTypes)) {
            $query->whereIn('location_type', $dto->locationTypes);
        }

        if ($dto->businessId) {
            $query->where('business_id', $dto->businessId);
        }
    }
}
