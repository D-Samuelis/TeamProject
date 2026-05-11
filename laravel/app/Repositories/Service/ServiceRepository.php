<?php

namespace App\Repositories\Service;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Builder;
use App\Application\DTO\SearchDTO;
use App\Application\DTO\ServiceSearchDTO;
use App\Domain\Service\Enums\ServiceRoleEnum;
use Illuminate\Support\Collection;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
=======
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Models\Business\Service;

use App\Domain\Service\Interfaces\ServiceRepositoryInterface;

use Illuminate\Support\Collection;
use App\Application\DTO\SearchDTO;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Service\Enums\ServiceRoleEnum;

class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * PUBLIC
     */
    public function findActive(int $id): Service
    {
        return Service::query()
            ->where('is_active', true)
            ->whereHas('business', fn($q) => $q->where('is_published', true))
            ->with(['assets' => fn($q) => $q->where('is_active', true)])
            ->findOrFail($id);
    }

    public function search(ServiceSearchDTO $dto, ?User $user = null)
    {
<<<<<<< HEAD
        $query = Service::query()->with(['business', 'branches', 'assets']);

        // Non-admins only see services belonging to their own businesses/branches
        if ($user && !$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('business.users', fn($q) => $q->where('user_id', $user->id))
                    ->orWhereHas('business.branches.users', fn($q) => $q->where('user_id', $user->id));
            });
        }

        if ($dto->statuses) {
            $query->where(function ($q) use ($dto) {
                if (in_array('deleted', $dto->statuses)) {
                    $q->orWhereNotNull('deleted_at');
                }
                if (in_array('active', $dto->statuses)) {
                    $q->orWhere('is_active', true);
                }
                if (in_array('inactive', $dto->statuses)) {
                    $q->orWhere('is_active', false);
                }
            });
        }

        // Text filters
        if ($dto->serviceName) {
            $query->where('name', 'like', '%' . $dto->serviceName . '%');
        }

        if ($dto->description) {
            $query->where('description', 'like', '%' . $dto->description . '%');
        }

        // Price / duration
        if ($dto->priceMin !== null) {
            $query->where('price', '>=', $dto->priceMin);
        }

        if ($dto->priceMax !== null) {
            $query->where('price', '<=', $dto->priceMax);
        }

        if ($dto->durationMin !== null) {
            $query->where('duration_minutes', '>=', $dto->durationMin);
        }

        if ($dto->durationMax !== null) {
            $query->where('duration_minutes', '<=', $dto->durationMax);
        }

        // Scoped to a specific business
        if ($dto->businessId) {
            $query->where('business_id', $dto->businessId);
        }

        // Admin: filter by user + optional role
        if ($user?->isAdmin() && $dto->userId) {
            $query->whereHas('users', function ($q) use ($dto) {
                $q->where('model_has_users.user_id', $dto->userId);
                if ($dto->role) {
                    $q->where('model_has_users.role', $dto->role);
                }
            });
        }

        return $query->latest()->paginate($dto->perPage, ['*'], 'page', $dto->page);
    }

    public function publicSearch(SearchDTO $dto)
    {
=======
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
        $query = Service::query()
            ->where('is_active', true)
            ->whereHas('business', fn($q) => $q->where('is_published', true));

        $this->applyServiceFilters($query, $dto);

        return $query->with(['business', 'branches'])->latest()->paginate($dto->perPage);
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

        if (!$user->isAdmin()) {
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
            ->with(['business', 'branches', 'assets', 'category'])
            ->latest()
            ->get();
    }

    public function findForManagement(int $id): Service
    {
        return Service::withTrashed()
            ->with(['business', 'branches', 'assets', 'category'])
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
     */
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

    public function update(Service $service, array $data): Service
    {
        if (isset($data['branch_ids'])) {
            $service->branches()->sync($data['branch_ids']);
            unset($data['branch_ids']);
        }

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

    public function restore(Service $service): Service
    {
        $service->update([
            'delete_after' => null,
            'is_active'    => true,
        ]);
        $service->restore();
        return $service;
    }

    /**
     * RELATIONSHIPS
     */
    public function attachBranches(Service $service, array $branchIds): void
    {
        $service->branches()->sync($branchIds);
    }

    public function attachUser(Service $service, int $userId, ServiceRoleEnum $role): void
    {
        $service->users()->attach($userId, ['role' => $role->value]);
    }

    public function detachUser(Service $service, int $userId): int
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
                    ->orWhereHas('business', fn($b) => $b->where('name', 'like', "%{$keyword}%"))
                    ->orWhereHas('branches', fn($br) => $br->where('city', 'like', "%{$keyword}%"));
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

        if ($dto->categoryId) {
            $query->where('category_id', $dto->categoryId);
        }
    }
}