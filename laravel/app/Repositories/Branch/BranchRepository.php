<?php

namespace App\Repositories\Branch;

use App\Application\DTO\BranchSearchDTO;
use Illuminate\Database\Eloquent\Builder;
use App\Application\DTO\SearchDTO;
use App\Domain\Branch\Enums\BranchRoleEnum;
use Illuminate\Support\Collection;
use App\Models\Business\Branch;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Models\Business\Branch;

use App\Domain\Branch\Interfaces\BranchRepositoryInterface;

use Illuminate\Support\Collection;
use App\Application\DTO\SearchDTO;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Branch\Enums\BranchRoleEnum;

class BranchRepository implements BranchRepositoryInterface
{
    /**
     * PUBLIC
     */
    public function findActive(int $id): Branch
    {
        return Branch::query()
            ->where('is_active', true)
            ->whereHas('business', fn($q) => $q->where('is_published', true))
            ->findOrFail($id);
    }

    public function search(BranchSearchDTO $dto, User $user)
    {
        $query = Branch::query()->with(['business', 'services']);

        if (!$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('business.users', fn($q) => $q->where('user_id', $user->id))
                    ->orWhereHas('users', fn($q) => $q->where('user_id', $user->id));
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

        if ($dto->branchName) {
            $query->where('name', 'like', '%' . $dto->branchName . '%');
        }

        if ($dto->branchName) {
            $query->where('city', 'like', '%' . $dto->city . '%');
        }

        if ($dto->country) {
            $query->where('country', 'like', '%' . $dto->country . '%');
        }

        if ($dto->address) {
            $query->where(function ($q) use ($dto) {
                $q->where('address_line_1', 'like', '%' . $dto->address . '%')
                    ->orWhere('address_line_2', 'like', '%' . $dto->address . '%');
            });
        }

        if ($dto->businessId) {
            $query->where('business_id', $dto->businessId);
        }

        if ($dto->types) {
            $query->whereIn('type', $dto->types);
        }

        return $query->latest()->paginate($dto->perPage, ['*'], 'page', $dto->page);
    }

    public function publicSearch(SearchDTO $dto)
    {
        $query = Branch::query()
            ->where('is_active', true)
            ->whereHas('business', fn($q) => $q->where('is_published', true));

        $this->applyBranchFilters($query, $dto);

        return $query
            ->with(['business', 'services.category'])
            ->latest()
            ->paginate($dto->perPage);
    }

    public function findMultipleByIds(array $ids): Collection
    {
        return Branch::whereIn('id', $ids)->get();
    }

    /**
     * MANAGEMENT
     */
    public function listForUser(User $user, ?Business $business = null, string $scope = 'active'): Collection
    {
        $query = Branch::query();

        if (!$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('business.users', fn($q) => $q->where('user_id', $user->id))
                    ->orWhereHas('users', fn($q) => $q->where('user_id', $user->id));
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
            ->with(['business', 'services', 'assets'])
            ->latest()
            ->get();
    }

    public function findForManagement(int $id): Branch
    {
        return Branch::withTrashed()
            ->with(['business', 'services', 'assets'])
            ->findOrFail($id);
    }

    public function findByBusinessId(int $businessId, string $scope = 'active'): Collection
    {
        $query = Branch::where('business_id', $businessId);

        match ($scope) {
            'deleted' => $query->onlyTrashed(),
            'all'     => $query->withTrashed(),
            default   => $query,
        };

        return $query->get();
    }

    public function findWithinBusiness(int $branchId, int $businessId): Branch
    {
        return Branch::where('id', $branchId)
            ->where('business_id', $businessId)
            ->firstOrFail();
    }

    /**
     * DATA PERSISTENCE
     */
    public function save(array $data): Branch
    {
        return Branch::create($data);
    }

    public function update(Branch $branch, array $data): Branch
    {
        $branch->update($data);
        return $branch;
    }

    public function delete(Branch $branch): void
    {
        $branch->update([
            'delete_after' => now()->addDays(7),
            'is_active' => false,
        ]);
        $branch->delete();
    }

    public function restore(Branch $branch): Branch
    {
        $branch->update(['delete_after' => null]);
        $branch->restore();
        return $branch;
    }

    /**
     * RELATIONSHIPS & ASSIGNMENTS
     */
    public function attachServices(Branch $branch, array $serviceIds): void
    {
        $branch->services()->sync($serviceIds);
    }

    public function attachUser(Branch $branch, int $userId, BranchRoleEnum $role): void
    {
        $branch->users()->attach($userId, ['role' => $role->value]);
    }

    public function detachUser(Branch $branch, int $userId): int
    {
        return $branch->users()->detach($userId);
    }

    public function getAssignments(Branch $branch): array
    {
        return [
            'services' => $branch->services()->pluck('id')->all(),
            'users'    => $branch->users()->pluck('id')->all(),
        ];
    }

    public function count(SearchDTO $dto): int
    {
        $query = Branch::query()
            ->where('is_active', true)
            ->whereHas('business', fn($q) => $q->where('is_published', true));

        $this->applyBranchFilters($query, $dto);

        return $query->count();
    }

    /**
     * PRIVATE HELPERS
     */
    private function applyBranchFilters(Builder $query, SearchDTO $dto): void
    {
        if ($dto->query) {
            $keyword = $dto->query;
            $query->where(function ($sub) use ($keyword) {
                $sub->where('name', 'like', "%{$keyword}%")
                    ->orWhere('city', 'like', "%{$keyword}%")
                    ->orWhereHas('business', fn($b) => $b->where('name', 'like', "%{$keyword}%"));
            });
        }

        if ($dto->city) {
            $query->where('city', $dto->city);
        }

        if ($dto->businessId) {
            $query->where('business_id', $dto->businessId);
        }

        if (!empty($dto->locationTypes)) {
            $query->whereIn('type', $dto->locationTypes);
        }

        if ($dto->categoryId) {
            $query->whereHas('services', function ($q) use ($dto) {
                $q->where('is_active', true)
                    ->where('services.category_id', $dto->categoryId);
            });
        }
    }
}