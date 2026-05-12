<?php

namespace App\Application\Branch\UseCases;

use App\Application\DTO\BranchSearchDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Models\Auth\User;

class ListBranches
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepo
    ) {}

    /**
     * Executes the list branches use case. It can list branches based on different scopes: 'active', 'deleted', 'all' for management mode, and 'public' for public browsing.
     * For management mode, it requires an authenticated user and checks their permissions to list branches. For public browsing, it applies search and filter criteria to list branches without requiring authentication.
     * @param User|null $user The authenticated user (required for management mode)
     * @param string $scope 'active'|'deleted'|'all'|'public'
     * @param array $filters Search/Filter criteria for public browsing
     * @return Collection A collection of Branch model instances matching the criteria.
     */
    public function execute(BranchSearchDTO $dto, ?User $user = null) {
        return $this->branchRepo->search($dto, $user);
    }
}
