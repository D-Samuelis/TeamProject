<?php

namespace App\Domain\Service\Interfaces;

use App\Application\DTO\SearchDTO;
use App\Models\Business\BranchService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BranchServiceRepositoryInterface
{
    /**
     * PUBLIC: End-user marketplace search.
     * Drives the explore/search page — returns enabled branch service instances
     * with full branch and business context.
     */
    public function search(SearchDTO $dto): LengthAwarePaginator;

    public function count(SearchDTO $dto): int;

    public function findActive(int $id): BranchService;

    /**
     * MANAGEMENT: Create a branch service instance from a template.
     */
    public function createInstance(array $data): BranchService;

    public function updateInstance(BranchService $branchService, array $data): BranchService;

    public function deleteInstance(BranchService $branchService): void;

    public function findById(int $id): ?BranchService;

    public function findForBranch(int $branchId, bool $enabledOnly = true): \Illuminate\Support\Collection;

    public function findWithinBranch(int $branchServiceId, int $branchId): BranchService;
}
