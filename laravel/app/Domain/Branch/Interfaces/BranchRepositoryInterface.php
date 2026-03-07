<?php

namespace App\Domain\Branch\Interfaces;

use App\Models\Business\Branch;
use Illuminate\Support\Collection;

interface BranchRepositoryInterface
{
    public function findById(int $id): Branch;
    public function findDeletedById(int $id): Branch;
    public function findByBusinessId(int $businessId): Collection;
    public function save(array $data): Branch;
    public function update(int $id, array $data): Branch;
    public function delete(int $id): void;
    public function restore(Branch $branch): void;
    public function attachServices(Branch $branch, array $serviceIds): void;
    public function attachUsers(Branch $branch, array $userIdsWithRoles): void;
    public function getAssignments(Branch $branch): array;
}