<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;

interface UserRepositoryInterface
{
    public function isAdmin(int $userId): bool;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function findByIds(array $ids): array;

    public function save(User $user): void;

    public function existsWithBusinessRole(int $userId, int $businessId, string $role): bool;

    public function existsWithBranchRole(int $userId, int $branchId, string $role): bool;
}
