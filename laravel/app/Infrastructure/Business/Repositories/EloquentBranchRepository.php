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
    public function create(array $data): DomainBranch
    {
        $branch = EloquentBranch::create($data);
        return $this->mapToDomain($branch);
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
            businessId: $branch->business_id,
            name: $branch->name,
            type: $branch->type,
            addressLine1: $branch->address_line_1,
            addressLine2: $branch->address_line_2,
            city: $branch->city,
            postalCode: $branch->postal_code,
            country: $branch->country,
            isActive: $branch->is_active
        );
    }
}
