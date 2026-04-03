<?php

namespace App\Domain\Service\Interfaces;

use Illuminate\Database\Eloquent\Builder;
use App\Application\DTO\SearchDTO;
use Illuminate\Support\Collection;
use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Models\Business\Service;

interface ServiceRepositoryInterface
{
    /**
     * PUBLIC
     * Note: public-facing service search (end-user marketplace) should use
     * BranchServiceRepository::search() instead, as it searches instances
     * with effective prices and branch context.
     * This findActive/search is for template-level lookups only.
     */
    public function findActive(int $id): Service;

    public function search(SearchDTO $dto);

    public function findMultipleByIds(array $ids): Collection;

    /**
     * MANAGEMENT
     */
    public function listForUser(User $user, ?Business $business = null, string $scope = 'active'): Collection;

    public function findForManagement(int $id): Service;

    public function findWithinBusiness(int $serviceId, int $businessId): Service;

    /**
     * DATA PERSISTENCE
     * Service::save/update only manages the template itself.
     * Branch instance creation is handled by BranchServiceRepository.
     */
    public function save(array $data): Service;

    public function update(Service $service, array $data): Service;

    public function delete(Service $service): void;

    public function restore(Service $service): void;

    public function count(SearchDTO $dto): int;
}
