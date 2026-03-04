<?php

namespace App\Infrastructure\Business\Repositories;

use App\Domain\Business\Repositories\BranchRepositoryInterface;
use App\Models\Business\Branch as EloquentBranch;
use App\Domain\Business\Entities\Branch as DomainBranch;

class EloquentBranchRepository implements BranchRepositoryInterface
{
    // Fetch a single branch by ID
    public function findById(int $id): ?DomainBranch
    {
        $branch = EloquentBranch::find($id);
        if (!$branch) return null;

        return $this->mapToDomain($branch);
    }

    // Fetch all branches for a business
    public function findByBusinessId(int $businessId): array
    {
        $branches = EloquentBranch::where('business_id', $businessId)->get();
        return $branches->map(fn($b) => $this->mapToDomain($b))->all();
    }

    // Create a new branch
    public function save(DomainBranch $branch): DomainBranch
    {
        $eloquent = EloquentBranch::create([
            'business_id'   => $branch->business_id,
            'name'          => $branch->name,
            'type'          => $branch->type,
            'address_line1' => $branch->address_line_1,
            'address_line2' => $branch->address_line_2,
            'city'          => $branch->city,
            'postal_code'   => $branch->postal_code,
            'country'       => $branch->country,
            'is_active'     => $branch->is_active,
        ]);

        return $this->mapToDomain($eloquent);
    }

    // Attach services to a branch
    public function attachServices(DomainBranch $branch, array $serviceIds): void
    {
        $eloquentBranch = EloquentBranch::findOrFail($branch->id);
        $eloquentBranch->services()->sync($serviceIds);
    }

    // Attach users with roles to a branch
    public function attachUsers(DomainBranch $branch, array $userIdsWithRoles): void
    {
        $eloquentBranch = EloquentBranch::findOrFail($branch->id);
        $eloquentBranch->users()->sync($userIdsWithRoles);
    }

    public function getAssignments(int $branchId): array
    {
        $branch = EloquentBranch::find($branchId);
        if (!$branch) {
            return [];
        }
        return [
            'services' => $branch->services()->pluck('id')->all(),
            'users' => $branch->users()->pluck('id')->all(),
        ];
    }

    // Map Eloquent model to domain entity
    private function mapToDomain(EloquentBranch $branch): DomainBranch
    {
        return new DomainBranch(
            id: $branch->id,
            business_id: $branch->business_id,
            name: $branch->name,
            type: $branch->type,
            address_line_1: $branch->address_line_1,
            address_line_2: $branch->address_line_2,
            city: $branch->city,
            postal_code: $branch->postal_code,
            country: $branch->country,
            is_active: $branch->is_active
        );
    }
}
