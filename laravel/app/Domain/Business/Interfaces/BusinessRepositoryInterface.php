<?php

namespace App\Domain\Business\Interfaces;

use Illuminate\Support\Collection;
use App\Models\Business\Business;
use App\Models\Auth\User;
use App\Domain\Business\Enums\BusinessRoleEnum;

interface BusinessRepositoryInterface
{
    public function findById(int $id): Business;
    public function findDeletedById(int $id): Business;
    public function save(array $data): Business;
    public function update(int $id, array $data): Business;
    public function delete(Business $business): void;
    public function restore(Business $business): void;
    public function existsOwner(int $userId): bool;
    public function attachUser(Business $business, int $userId, BusinessRoleEnum $role): void;
    public function listForUser(User $user, string $scope = 'active', bool $loadRelations = false): Collection;
}
