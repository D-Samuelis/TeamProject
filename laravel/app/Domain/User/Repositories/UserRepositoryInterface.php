<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User as DomainUser;

interface UserRepositoryInterface
{
    public function findById(int $id): ?DomainUser;

    public function findByEmail(string $email): ?DomainUser;

    public function findByIds(array $ids): array;

    public function save(DomainUser $user): DomainUser;

    public function existsWithBusinessRole(int $userId, int $businessId, string $role): bool;

    public function existsWithBranchRole(int $userId, int $branchId, string $role): bool;
}