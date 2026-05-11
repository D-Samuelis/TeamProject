<?php

namespace App\Application\Business\UseCases;

use App\Application\DTO\BusinessSearchDTO;
use App\Models\Auth\User;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;

class ListBusinesses
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo
    ) {}

    /**
     * Executes the list businesses use case. It can list businesses based on different scopes: 'active', 'deleted', 'all' for management mode, and 'public' for public browsing.
     * For management mode, it requires an authenticated user and checks their permissions to list businesses. For public browsing, it applies search and filter criteria to list businesses without requiring authentication.
     * @param User|null $user The authenticated user (required for management mode)
     * @param string $scope 'active'|'deleted'|'all'|'public'
     * @param array $filters Search/Filter criteria for public browsing
     * @return Collection A collection of Business model instances matching the criteria.
     */
    public function execute(BusinessSearchDTO $dto, ?User $user = null) {
        if ($user && !$user->isAdmin()) {
            $dto = new BusinessSearchDTO(
                businessName: $dto->businessName,
                description: $dto->description,
                statuses: $dto->statuses,
                published: $dto->published,
                userId: null,
                role: $dto->role,
                categoryId: $dto->categoryId,
                perPage: $dto->perPage,
                page: $dto->page,
            );
        }

        return $this->businessRepo->search($dto, $user);
    }
}
