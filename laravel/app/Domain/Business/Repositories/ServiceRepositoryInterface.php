<?php

namespace App\Domain\Business\Repositories;

use Illuminate\Support\Collection;
use App\Models\Business\Service;

interface ServiceRepositoryInterface
{
    public function findById(int $id): ?Service;

    public function findByBusinessId(int $businessId): Collection;

    public function save(array $data): Service;

    public function attachBranches(Service $service, array $branchIds): void;

    public function attachUsers(Service $service, array $userIdsWithRoles): void;
}