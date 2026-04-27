<?php

namespace App\Application\Service\UseCases;

use App\Models\Auth\User;
use App\Models\Business\Business;

use Illuminate\Support\Collection;
use App\Application\DTO\SearchDTO;

use App\Domain\Service\Interfaces\ServiceRepositoryInterface;

class ListServices
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo
    ) {}

    /**
     * Executes the list services use case. It can list services based on different scopes: 'active', 'deleted', 'all' for management mode, and 'public' for public browsing.
     * For management mode, it requires an authenticated user and checks their permissions to list services. For public browsing, it applies search and filter criteria to list services without requiring authentication.
     * @param User|null $user The authenticated user (required for management mode)
     * @param string $scope 'active'|'deleted'|'all'|'public'
     * @param array $filters Search/Filter criteria for public browsing
     * @return Collection A collection of Service model instances matching the criteria.
     */
    public function execute(
        ?User $user = null,
        ?Business $business = null,
        string $scope = 'active',
        array $filters = []
    ): Collection {
        if ($scope === 'public') {
            $dto = SearchDTO::fromArray($filters);
            return $this->serviceRepo->search($dto)->getCollection();
        }

        if (!$user) {
            throw new \InvalidArgumentException('User is required for non-public service lists.');
        }

        return $this->serviceRepo->listForUser($user, $business, $scope);
    }
}
