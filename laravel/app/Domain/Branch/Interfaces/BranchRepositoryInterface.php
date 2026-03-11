<?php

namespace App\Domain\Branch\Interfaces;

use App\Models\Business\Branch;
use Illuminate\Support\Collection;
use App\Application\DTO\SearchDTO;
use App\Domain\Branch\Enums\BranchRoleEnum;

interface BranchRepositoryInterface
{
    /**
     * PUBLIC: Search and find active branches.
     */
    public function findActive(int $id): Branch;

    public function search(SearchDTO $dto);

    public function findMultipleByIds(array $ids): Collection;
    /**
     * MANAGEMENT: Operations for owners/admins.
     */
    public function findForManagement(int $id): Branch;

    public function findByBusinessId(int $businessId, string $scope = 'active'): Collection;

    /**
     * DATA PERSISTENCE
     */
    public function save(array $data): Branch;

    public function update(Branch $branch, array $data): Branch;

    public function delete(Branch $branch): void;

    public function restore(Branch $branch): void;

    /**
     * RELATIONSHIPS & ASSIGNMENTS
     */
    public function attachServices(Branch $branch, array $serviceIds): void;
    
    public function attachUser(Branch $branch, int $userId, BranchRoleEnum $role): void;

    public function detachUser($branch, $userId): Branch;
    
    public function getAssignments(Branch $branch): array;

    public function count(SearchDTO $dto): int;
}
