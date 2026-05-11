<?php

namespace App\Application\Branch\UseCases;

<<<<<<< HEAD
use App\Application\DTO\BranchSearchDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Models\Auth\User;
=======
use App\Models\Auth\User;
use App\Models\Business\Business;

use Illuminate\Support\Collection;
use App\Application\DTO\SearchDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;

use App\Exceptions\InvalidScopeException;
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf

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
<<<<<<< HEAD
    public function execute(BranchSearchDTO $dto, ?User $user = null) {
        return $this->branchRepo->search($dto, $user);
=======
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
            throw new InvalidScopeException('User is required for this scope.');
        }

        return $this->branchRepo->listForUser($user, $business, $scope);
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
    }
}
