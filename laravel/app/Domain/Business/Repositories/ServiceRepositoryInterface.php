<?php

namespace App\Domain\Business\Repositories;

use App\Domain\Business\Entities\Service;

interface ServiceRepositoryInterface
{
    public function findById(int $id): ?Service;

    public function findByBusinessId(int $businessId): array;

    public function create(array $data): Service;

    public function attachBranches(Service $service, array $branchIds): void;

    public function attachUsers(Service $service, array $userIdsWithRoles): void;
}