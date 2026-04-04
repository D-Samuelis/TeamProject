<?php

namespace App\Application\Business\UseCases;

use App\Application\DTO\SearchDTO;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Models\Auth\User;
use Illuminate\Support\Collection;

class ListBusinesses
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo
    ) {}

    /**
     * @param User|null $user The authenticated user (required for management mode)
     * @param string $scope 'active'|'deleted'|'all'|'public'
     * @param array $filters Search/Filter criteria for public browsing
     */
    public function execute(?User $user = null, string $scope = 'active', array $filters = []): Collection
    {
        if (!$user) {
            // Public browsing ignores scope
            $dto = SearchDTO::fromArray($filters);
            return $this->businessRepo->search($dto)->getCollection();
        }

        return $this->businessRepo->listForUser($user, $scope);
    }
}
