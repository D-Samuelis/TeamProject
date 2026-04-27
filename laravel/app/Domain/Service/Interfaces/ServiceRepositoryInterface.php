<?php

namespace App\Domain\Service\Interfaces;

use App\Application\DTO\ServiceSearchDTO;
use Illuminate\Support\Collection;
use App\Models\Business\Service;
use App\Application\DTO\SearchDTO;
use App\Domain\Service\Enums\ServiceRoleEnum;
use App\Models\Auth\User;
use App\Models\Business\Business;

interface ServiceRepositoryInterface
{
    /**
     * PUBLIC
     */
    public function findActive(int $id): Service;

    public function search(ServiceSearchDTO $dto, User $user = null);

    public function publicSearch(SearchDTO $dto);

    public function findMultipleByIds(array $ids): Collection;

    /**
     * MANAGEMENT
     */
    public function listForUser(User $user, ?Business $business = null, string $scope = 'active'): Collection;

    public function findForManagement(int $id): Service;

    public function findWithinBusiness(int $serviceId, int $businessId): Service;

    /**
     * DATA PERSISTENCE
     */
    public function save(array $data): Service;

    public function update(Service $service, array $data): Service;

    public function delete(Service $service): void;

    public function restore(Service $service): Service;

    /**
     * RELATIONSHIPS
     */
    public function attachBranches(Service $service, array $branchIds): void;

    public function attachUser(Service $service, int $userId, ServiceRoleEnum $role): void;

    public function detachUser(Service $service, int $userId): int;

    public function count(SearchDTO $dto): int;
}
