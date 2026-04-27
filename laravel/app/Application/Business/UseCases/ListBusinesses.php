<?php

namespace App\Application\Business\UseCases;

use App\Application\DTO\BusinessSearchDTO;
use App\Models\Auth\User;

<<<<<<< HEAD
=======
use Illuminate\Support\Collection;
use App\Application\DTO\SearchDTO;

>>>>>>> 97c82cc ([FEAT] Added exception handling for Beanch and Service models.)
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;

use App\Exceptions\InvalidScopeException;

class ListBusinesses
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo
    ) {}

    /**
     * Executes the list businesses use case. It can list businesses based on different scopes: 'active', 'deleted', 'all' for management mode, and 'public' for public browsing.
     * For management mode, it requires an authenticated user and checks their permissions to list businesses. For public browsing, it applies search and filter criteria to list businesses without requiring authentication.
     * @param User|null $user The authenticated user (required for management mode)
<<<<<<< HEAD
=======
     * @param string $scope 'active'|'deleted'|'all'|'public'
     * @param array $filters Search/Filter criteria for public browsing
     * @return Collection A collection of Business model instances matching the criteria.
>>>>>>> 97c82cc ([FEAT] Added exception handling for Beanch and Service models.)
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

<<<<<<< HEAD
        return $this->businessRepo->search($dto, $user);
=======
        if (!$user) {
            throw new InvalidScopeException('User is required for non-public business lists.');
        }

        return $this->businessRepo->listForUser($user, $scope);
>>>>>>> 97c82cc ([FEAT] Added exception handling for Beanch and Service models.)
    }
}
