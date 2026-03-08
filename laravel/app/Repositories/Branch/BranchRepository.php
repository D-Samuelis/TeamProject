<?php

namespace App\Repositories\Branch;

use Illuminate\Support\Collection;
use App\Models\Business\Branch;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;

class BranchRepository implements BranchRepositoryInterface
{
    public function findById(int $id, bool $withTrashed = false): Branch
    {
        $query = Branch::query();
        if ($withTrashed) {
            $query->withTrashed();
        }
        return $query->findOrFail($id);
    }

    public function findDeletedById(int $id): Branch
    {
        return Branch::withTrashed()->findOrFail($id);
    }

    public function findByBusinessId(int $businessId): Collection
    {
        return Branch::where('business_id', $businessId)->get();
    }

    public function save(array $data): Branch
    {
        return Branch::create($data);
    }

    public function update(int $id, array $data): Branch
    {
        $branch = $this->findById($id);
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
