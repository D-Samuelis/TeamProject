<?php
namespace App\Domain\Business\Repositories;

use App\Domain\Business\Entities\Branch;

interface BranchRepositoryInterface
{
    public function findById(int $id): ?Branch;

    public function findByBusinessId(int $businessId): array;

    public function create(array $data): Branch;

    public function attachServices(Branch $branch, array $serviceIds): void;

    public function attachUsers(Branch $branch, array $userIdsWithRoles): void;

    public function getAssignments(int $branchId): array;
}