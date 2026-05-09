<?php

namespace App\Application\Branch\UseCases;

use App\Application\DTO\SearchDTO;
use App\Application\DTO\BranchSearchDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Business;
use Illuminate\Support\Collection;

class ListBranches
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepo
    ) {}

    /**
     * @param User|null $user The authenticated user (required for management mode)
     * @param string $scope 'active'|'deleted'|'all'|'public'
     * @param array $filters Search/Filter criteria for public browsing
     */
    public function execute(BranchSearchDTO $dto, ?User $user = null) {
        return $this->branchRepo->search($dto, $user);
    }
}
