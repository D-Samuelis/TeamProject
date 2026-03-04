<?php
namespace App\Domain\Business\Repositories;

use App\Domain\Business\Entities\Branch as DomainBranch;

interface BranchRepositoryInterface
{
    public function findById(int $id): ?DomainBranch;

    public function findByBusinessId(int $businessId): array;

    public function save(DomainBranch $data): DomainBranch;

    public function attachServices(DomainBranch $branch, array $serviceIds): void;

    public function attachUsers(DomainBranch $branch, array $userIdsWithRoles): void;

    public function getAssignments(int $branchId): array;
}