<?php

namespace App\Domain\Business\Interfaces;

use App\Models\Business\Business;
use App\Domain\Business\Enums\BusinessRoleEnum;
use Illuminate\Support\Collection;

interface BusinessRepositoryInterface
{
    public function findById(int $id): Business;
    public function findDeletedById(int $id): Business;
    public function findByUserId(int $userId): Collection;
    public function save(array $data): Business;
    public function update(int $id, array $data): Business;
    public function delete(Business $business): void;
    public function restore(Business $business): void;
    public function existsOwner(int $userId): bool;
    public function allWithRelations(string $scope = 'active'): Collection;
    public function attachUser(Business $business, int $userId, BusinessRoleEnum $role): void;
    public function findByIdWithRelations(int $id): Business;
}
