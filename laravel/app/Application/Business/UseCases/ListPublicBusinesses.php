<?php

namespace App\Application\Business\UseCases;

use App\Models\Auth\User;
use Illuminate\Support\Collection;

use App\Application\DTO\SearchDTO;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;

use App\Exceptions\Business\InvalidBusinessScopeException;

/**
 * Use case for listing businesses. It can list businesses based on different scopes: 'active', 'deleted', 'all' for management mode, and 'public' for public browsing.
 * For management mode, it requires an authenticated user and checks their permissions to list businesses. For public browsing, it applies search and filter criteria to list businesses without requiring authentication.
 * Throws InvalidArgumentException if the scope is invalid or if a user is required but not provided. Returns a collection of Business model instances based on the specified scope and filters.
 * @param User|null $user The authenticated user (required for management mode)
 * @param string $scope 'active'|'deleted'|'all'|'public'
 * @param array $filters Search/Filter criteria for public browsing
 * @return Collection A collection of Business model instances matching the criteria.
 * @throws InvalidArgumentException If the scope is invalid or if a user is required but not provided.
 */
class ListPublicBusinesses
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo
    ) {}

    /**
     * @param User|null $user The authenticated user (required for management mode)
     * @param string $scope 'active'|'deleted'|'all'|'public'
     * @param array $filters Search/Filter criteria for public browsing
     */
    public function execute(
        ?User $user = null,
        string $scope = 'active',
        array $filters = []
    ): Collection {
        if ($scope === 'public') {
            $dto = SearchDTO::fromArray($filters);
            return $this->businessRepo->publicSearch($dto)->getCollection();
        }

        if (!$user) {
            throw new InvalidBusinessScopeException();
        }

        return $this->businessRepo->listForUser($user, $scope);
    }
}
