<?php

namespace App\Repositories\Business;

use App\Application\Business\DTO\SearchDTO;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;

class BusinessRepository implements BusinessRepositoryInterface
{
    /**
     * PUBLIC METHODS
     */

    /**
     * Find a business for the public profile.
     * Strictly active and published only.
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

    /**
     * The search engine.
     * Keyword matching and parameter filtering.
     */
    public function search(SearchDTO $dto): Collection
    {
        $query = Business::query()->where('is_published', true);

        $this->applySearchFilters($query, $dto);

        return $query
            ->with([
                // Active only
                'branches' => fn($q) => $q->where('is_active', true),
                'services' => fn($q) => $q->where('is_active', true),
            ])
            ->latest()
            ->get();
    }

    /**
     * MANAGEMENT METHODS
     */

    /**
     * List businesses for the owner.
     */
    public function listForOwner(User $user, string $scope = 'active'): Collection
    {
        $query = Business::query();

        if (!$user->isAdmin()) {
            $query->whereHas('users', fn($q) => $q->where('user_id', $user->id));
        }

        match ($scope) {
            'deleted' => $query->onlyTrashed(),
            'all' => $query->withTrashed(),
            default => $query, // active
        };

        return $query
            ->with(['branches', 'services'])
            ->latest()
            ->get();
    }

    /**
     * Find for management.
     * Includes trashed records.
     */
    public function findForManagement(int $id): Business
    {
        return Business::withTrashed()
            ->with([
                'branches' => fn($q) => $q->withTrashed(),
                'services' => fn($q) => $q->withTrashed(),
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
            $q->where('user_id', $userId)->wherePivot('role', BusinessRoleEnum::OWNER->value);
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

    public function detachUser($business, $userId)
    {
        return $business->users()->detach($userId);
    }

    /**
     * -------------------------------------------------------------------------
     * PRIVATE HELPERS
     * -------------------------------------------------------------------------
     */
    private function applySearchFilters(Builder $query, SearchDTO $dto): void
    {
        // Keyword Deep Search
        if ($dto->query) {
            $keyword = $dto->query;

            $query->where(function ($sub) use ($keyword) {
                // Check Business Name/Description
                $sub->orWhere('name', 'like', "%{$keyword}%")->orWhere('description', 'like', "%{$keyword}%");

                // Check if any ACTIVE branch matches city or name
                $sub->orWhereHas('branches', fn($b) => $b->where('is_active', true)->where(fn($q) => $q->where('name', 'like', "%{$keyword}%")->orWhere('city', 'like', "%{$keyword}%")));

                // Check if any ACTIVE service matches name or description
                $sub->orWhereHas('services', fn($s) => $s->where('is_active', true)->where(fn($q) => $q->where('name', 'like', "%{$keyword}%")->orWhere('description', 'like', "%{$keyword}%")));
            });
        }

        // Other Exact Column Checks
        if ($dto->city) {
            $query->whereHas('branches', fn($q) => $q->where('is_active', true)->where('city', $dto->city));
        }

        if ($dto->maxPrice) {
            $query->whereHas('services', fn($q) => $q->where('is_active', true)->where('price', '<=', $dto->maxPrice));
        }

        if ($dto->maxDuration) {
            $query->whereHas('services', fn($q) => $q->where('is_active', true)->where('duration_minutes', '<=', $dto->maxDuration));
        }

        if (!empty($dto->locationTypes)) {
            $query->whereHas('services', fn($q) => $q->where('is_active', true)->whereIn('location_type', $dto->locationTypes));
        }
    }
}
