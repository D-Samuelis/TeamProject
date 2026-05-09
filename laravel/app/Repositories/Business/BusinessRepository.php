<?php

namespace App\Repositories\Business;

use App\Application\DTO\SearchDTO;
use App\Application\DTO\BusinessSearchDTO;
use App\Domain\Business\Enums\BusinessStateEnum;
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
    public function findActive(int $id): Business | null
    {
        return Business::query()
            ->where('is_published', true)
            ->with([
                'branches' => fn($q) => $q->where('is_active', true)->with([
                    'services' => fn($sq) => $sq->where('is_active', true)->with('category'),
                ]),
                'services' => fn($q) => $q->where('is_active', true)->with('category'),
            ])
            ->find($id);
    }

    public function search(BusinessSearchDTO $dto, ?User $user = null)
    {
        $query = Business::query()
            ->with([
                'branches' => fn($q) => $q->where('is_active', true)
                    ->with(['services' => fn($sq) => $sq->where('is_active', true)]),
                'services' => fn($q) => $q->where('is_active', true),
            ]);

        if ($dto->statuses) {
            $query->withTrashed()->where(function ($q) use ($dto) {
                $stateValues = array_column(BusinessStateEnum::cases(), 'value');
                $filteredStates = array_intersect($stateValues, $dto->statuses);

                if (in_array('deleted', $dto->statuses)) {
                    $q->orWhereNotNull('deleted_at');
                }
                if ($filteredStates) {
                    $q->orWhere(function ($q2) use ($filteredStates) {
                        $q2->whereIn('state', $filteredStates);

                    });
                }
            });
        }
        // default: active only (no trashed)

        // --- Ownership: non-admins only see their own businesses ---
        if ($user && !$user->isAdmin()) {
            $query->whereHas('users', fn($q) => $q->where('user_id', $user->id));
        }

        // --- Admin filtering by specific user ---
        if ($user?->isAdmin() && $dto->userId) {
            $query->whereHas('users', function ($q) use ($dto) {
                $q->where('model_has_users.user_id', $dto->userId);
                if ($dto->role) {
                    $q->where('model_has_users.role', $dto->role);
                }
            });
        }

        // --- Published filter ---
        if ($dto->published === 'yes') {
            $query->where('is_published', true);
        } elseif ($dto->published === 'no') {
            $query->where('is_published', false);
        }

        // --- Business name ---
        if ($dto->businessName) {
            $query->where('name', 'like', '%' . $dto->businessName . '%');
        }

        // --- Description keyword ---
        if ($dto->description) {
            $query->where('description', 'like', '%' . $dto->description . '%');
        }

        // --- Category (via services) ---
        if ($dto->categoryId) {
            $query->whereHas('services', fn($q) => $q->where('category_id', $dto->categoryId));
        }

        return $query
            ->latest()
            ->paginate($dto->perPage, ['*'], 'page', $dto->page);
    }

    public function publicSearch(SearchDTO $dto)
    {
        $query = Business::query()->where('is_published', true);

        $this->applySearchFilters($query, $dto);

        return $query
            ->with([
                'branches' => fn($q) => $q->where('is_active', true)->with([
                    'services' => fn($sq) => $sq->where('is_active', true)->with('category'),
                ]),
                'services' => fn($q) => $q->where('is_active', true)->with('category'),
            ])
            ->latest()
            ->paginate($dto->perPage);
    }

    /**
     * MANAGEMENT METHODS
     */
    public function listForUser(User $user, string $scope = 'active'): Collection
    {
        $query = Business::query();

        if (!$user->isAdmin()) {
            $query->whereHas('users', fn($q) => $q->where('user_id', $user->id));
        }

        match ($scope) {
            'deleted' => $query->onlyTrashed(),
            'all'     => $query->withTrashed(),
            default   => $query,
        };

        return $query
            ->with(['branches', 'services'])
            ->latest()
            ->get();
    }

    public function findForManagement(int $id): Business | null
    {
        return Business::withTrashed()
            ->with([
                'branches' => fn($q) => $q->withTrashed(),
                'services' => fn($q) => $q->withTrashed(),
            ])
            ->find($id);
    }

    /**
     * DATA PERSISTENCE
     */
    public function save(array $data): Business | null
    {
        return Business::create($data);
    }

    public function update(Business $business, array $data): Business | null
    {
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
            $q->where('model_has_users.user_id', $userId)
                ->where('model_has_users.role', BusinessRoleEnum::OWNER->value);
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

        // Reuse the exact same filter logic
        $this->applySearchFilters($query, $dto);

        return $query->count();
    }

    /**
     * PRIVATE HELPERS
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

        if ($dto->categoryId) {
            $query->whereHas('branches', function ($branchQuery) use ($dto) {
                $branchQuery->where('is_active', true)
                    ->whereHas('services', function ($serviceQuery) use ($dto) {
                        $serviceQuery->where('is_active', true)
                            ->where('services.category_id', $dto->categoryId);
                    });
            });
        }
    }
}
