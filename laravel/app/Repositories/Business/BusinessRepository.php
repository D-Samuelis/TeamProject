<?php

namespace App\Repositories\Business;

use App\Application\DTO\SearchDTO;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;

class BusinessRepository implements BusinessRepositoryInterface
{
    /**
     * PUBLIC
     */
    public function findActive(int $id): Business
    {
        return Business::query()
            ->where('is_published', true)
            ->with([
                'branches' => fn($q) => $q->where('is_active', true),
                'services' => fn($q) => $q->where('is_active', true),
            ])
            ->findOrFail($id);
    }

    public function search(SearchDTO $dto)
    {
        $query = Business::query()->where('is_published', true);

        $this->applySearchFilters($query, $dto);

        return $query
            ->with([
                'branches' => fn($q) => $q->where('is_active', true),
                'services' => fn($q) => $q->where('is_active', true),
            ])
            ->latest()
            ->paginate($dto->perPage);
    }

    /**
     * MANAGEMENT
     */
    public function listForUser(User $user, string $scope = 'active'): Collection
    {
        $query = Business::query();

        if (!$user->isAdmin()) {
            $query->whereHas('users', fn($q) => $q->where('user_id', $user->id));
        }

        match ($scope) {
            'deleted' => $query->onlyTrashed(),
            'all' => $query->withTrashed(),
            default => $query,
        };

        return $query
            ->with(['branches', 'services'])
            ->latest()
            ->get();
    }

    public function findForManagement(int $id): Business
    {
        return Business::withTrashed()
            ->with([
                'users',
                'branches.users',
                'branches.branchServices.users',
                'branches.branchServices.service',
            ])
            ->findOrFail($id);
    }

    /**
     * DATA PERSISTENCE
     */
    public function save(array $data): Business
    {
        return Business::create($data);
    }

    public function update(int $id, array $data): Business
    {
        $business = $this->findForManagement($id);
        $business->update($data);
        return $business;
    }

    public function delete(Business $business): void
    {
        $business->update([
            'delete_after' => now()->addDays(7),
            'is_published' => false,
        ]);
        $business->delete();
    }

    public function restore(Business $business): void
    {
        $business->update(['delete_after' => null]);
        $business->restore();
    }

    public function existsOwner(int $userId, ?int $businessId = null): bool
    {
        $query = Business::whereHas('users', function ($q) use ($userId) {
            $q->where('model_has_users.user_id', $userId)->where('model_has_users.role', BusinessRoleEnum::OWNER->value);
        });

        if ($businessId) {
            $query->where('id', $businessId);
        }

        return $query->exists();
    }

    public function attachUser(Business $business, int $userId, BusinessRoleEnum $role): void
    {
        $business->users()->attach($userId, ['role' => $role->value]);
    }

    public function detachUser(Business $business, int $userId): int
    {
        return $business->users()->detach($userId);
    }

    public function count(SearchDTO $dto): int
    {
        $query = Business::query()->where('is_published', true);

        $this->applySearchFilters($query, $dto);

        return $query->count();
    }

    /**
     * PRIVATE HELPERS
     */
    private function applySearchFilters(Builder $query, SearchDTO $dto): void
    {
        if ($dto->query) {
            $keyword = $dto->query;
            $query->where(function ($sub) use ($keyword) {
                $sub->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas('branches', fn($b) => $b->where('is_active', true)->where(fn($q) => $q->where('name', 'like', "%{$keyword}%")->orWhere('city', 'like', "%{$keyword}%")))
                    ->orWhereHas('services', fn($s) => $s->where('is_active', true)->where(fn($q) => $q->where('name', 'like', "%{$keyword}%")->orWhere('description', 'like', "%{$keyword}%")));
            });
        }

        if ($dto->city) {
            $query->whereHas('branches', fn($q) => $q->where('is_active', true)->where('city', $dto->city));
        }

        // Filter against base values on the service template
        if ($dto->maxPrice) {
            $query->whereHas('services', fn($q) => $q->where('is_active', true)->where('base_price', '<=', $dto->maxPrice));
        }

        if ($dto->maxDuration) {
            $query->whereHas('services', fn($q) => $q->where('is_active', true)->where('base_duration_minutes', '<=', $dto->maxDuration));
        }

        if (!empty($dto->locationTypes)) {
            $query->whereHas('services', fn($q) => $q->where('is_active', true)->whereIn('location_type', $dto->locationTypes));
        }
    }
}
