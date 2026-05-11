<?php

namespace App\Domain\Branch\Interfaces;

<<<<<<< HEAD
use App\Application\DTO\BranchSearchDTO;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use App\Models\Auth\User;
=======
use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Models\Business\Branch;
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf

use Illuminate\Support\Collection;
use App\Application\DTO\SearchDTO;

use App\Domain\Branch\Enums\BranchRoleEnum;

interface BranchRepositoryInterface
{
    /**
     * PUBLIC: Search and find active branches. Throws ModelNotFoundException if not found.
     */
    public function findActive(int $id): Branch;

    public function search(BranchSearchDTO $dto, User $user);
    public function publicSearch(SearchDTO $dto);

    public function findMultipleByIds(array $ids): Collection;

    /**
     * MANAGEMENT: Operations for owners/admins. Throws ModelNotFoundException if not found.
     */
    public function listForUser(User $user, ?Business $business = null, string $scope = 'active'): Collection;

    public function findForManagement(int $id): Branch;

    public function findByBusinessId(int $businessId, string $scope = 'active'): Collection;

    public function findWithinBusiness(int $branchId, int $businessId): Branch;

    /**
     * DATA PERSISTENCE
     */
    public function save(array $data): Branch;

    public function update(Branch $branch, array $data): Branch;

    public function delete(Branch $branch): void;

    public function restore(Branch $branch): Branch;

    /**
     * RELATIONSHIPS & ASSIGNMENTS
     */
    public function attachServices(Branch $branch, array $serviceIds): void;

    public function attachUser(Branch $branch, int $userId, BranchRoleEnum $role): void;

    public function detachUser(Branch $branch, int $userId): int;

    public function getAssignments(Branch $branch): array;

    public function count(SearchDTO $dto): int;
}