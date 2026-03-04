<?php

namespace App\Domain\Business\Repositories;

use App\Domain\Business\Entities\Service as DomainService;

interface ServiceRepositoryInterface
{
    public function findById(int $id): ?DomainService;

    public function findByBusinessId(int $businessId): array;

    public function save(DomainService $data): DomainService;

    public function attachBranches(DomainService $service, array $branch_ids): void;

    public function attachUsers(DomainService $service, array $userIdsWithRoles): void;
}