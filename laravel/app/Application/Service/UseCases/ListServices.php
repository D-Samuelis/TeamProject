<?php

namespace App\Application\Service\UseCases;

<<<<<<< HEAD
use App\Application\DTO\ServiceSearchDTO;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Auth\User;
=======
use App\Models\Auth\User;
use App\Models\Business\Business;

use Illuminate\Support\Collection;
use App\Application\DTO\SearchDTO;

use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf

class ListServices
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo
    ) {}

<<<<<<< HEAD
    public function execute(ServiceSearchDTO $dto, ?User $user = null) {
        if ($user && !$user->isAdmin()) {
            $dto = new ServiceSearchDTO(
                serviceName: $dto->serviceName,
                description: $dto->description,
                priceMin: $dto->priceMin,
                priceMax: $dto->priceMax,
                durationMin: $dto->durationMin,
                durationMax: $dto->durationMax,
                statuses: $dto->statuses,
                businessId: $dto->businessId,
                userId: null,
                role: $dto->role,
                perPage: $dto->perPage,
                page: $dto->page,
            );
=======
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
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
        }

        return $this->serviceRepo->search($dto, $user);
    }
}
