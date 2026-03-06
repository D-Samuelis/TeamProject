<?php

namespace App\Repositories\Branch;

use Illuminate\Support\Collection;
use App\Models\Business\Branch;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;

class BranchRepository implements BranchRepositoryInterface
{
    public function findById(int $id): ?Branch
    {
        return Branch::find($id);
    }

    public function findByBusinessId(int $businessId): Collection
    {
        return Branch::where('business_id', $businessId)->get();
    }

    public function save(array $data): Branch
    {
        return Branch::create($data);
    }

    public function attachServices(Branch $branch, array $serviceIds): void
    {
        $branch->services()->sync($serviceIds);
    }

    public function attachUsers(Branch $branch, array $userIdsWithRoles): void
    {
        // $userIdsWithRoles = [userId => ['role' => 'manager'], ...]
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