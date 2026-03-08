<?php

namespace App\Domain\Service\Interfaces;

use Illuminate\Support\Collection;
use App\Models\Business\Service;

interface ServiceRepositoryInterface
{
    public function findById(int $id): ?Service;
    public function findByBusinessId(int $businessId): Collection;
    public function save(array $data): Service;
    //public function update(int $id, array $data): Service;
    public function attachBranches(Service $service, array $branchIds): void;
    public function attachUsers(Service $service, array $userIdsWithRoles): void;
}