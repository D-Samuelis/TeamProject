<?php

namespace App\Domain\Service\Interfaces;

use Illuminate\Support\Collection;
use App\Models\Business\Service;
use App\Application\Business\DTO\SearchDTO;

interface ServiceRepositoryInterface
{
    /**
     * PUBLIC
     */
    public function findActive(int $id): Service;
    
    public function search(SearchDTO $dto): Collection;

    /**
     * MANAGEMENT
     */
    public function findForManagement(int $id): Service;

    /**
     * DATA PERSISTENCE
     */
    public function save(array $data): Service;
    
    public function update(Service $service, array $data): Service;
    
    public function delete(Service $service): void;
    
    public function restore(Service $service): void;

    /**
     * RELATIONSHIPS
     */
    public function attachBranches(Service $service, array $branchIds): void;
    
    public function attachUsers(Service $service, array $userIdsWithRoles): void;
}