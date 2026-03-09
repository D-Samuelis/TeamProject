<?php

namespace App\Repositories\Branch;

use App\Application\Business\DTO\SearchDTO;
use Illuminate\Support\Collection;
use App\Models\Business\Branch;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;

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

    public function search(SearchDTO $dto): Collection
    {
        $query = Branch::query()
            ->where('is_active', true)
            ->whereHas('business', fn($q) => $q->where('is_published', true));

        if ($dto->businessId) $query->where('business_id', $dto->businessId);
        if ($dto->city) $query->where('city', $dto->city);

        if ($dto->query) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$dto->query}%")
                ->orWhere('city', 'like', "%{$dto->query}%"));
        }

        return $query->with('business')->get();
    }

    /**
     * MANAGEMENT
     */
    public function findForManagement(int $id): Branch
    {
        return Branch::withTrashed()->findOrFail($id);
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

    public function restore(Branch $branch): void
    {
        $branch->update(['delete_after' => null]);
        $branch->restore();
    }

    public function attachServices(Branch $branch, array $serviceIds): void
    {
        $branch->services()->sync($serviceIds);
    }

    public function attachUsers(Branch $branch, array $userIdsWithRoles): void
    {
        $branch->users()->sync($userIdsWithRoles);
    }

    public function getAssignments(Branch $branch): array
    {
        return [
            'services' => $branch->services()->pluck('id')->all(),
            'users' => $branch->users()->pluck('id')->all(),
        ];
    }
}
