<?php

namespace App\Application\Branch\UseCases;

use App\Application\DTO\SearchDTO;
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
    public function execute(
        ?User $user = null,
        ?Business $business = null,
        string $scope = 'active',
        array $filters = []
    ): Collection {
        if ($scope === 'public') {
            $dto = SearchDTO::fromArray($filters);
            return $this->branchRepo->search($dto)->getCollection();
        }

        if (!$user) {
            throw new \InvalidArgumentException('User is required for non-public branch lists.');
        }

        return $this->branchRepo->listForUser($user, $business, $scope);
    }
}
