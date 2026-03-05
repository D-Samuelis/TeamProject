<?php

namespace App\Domain\Business\Repositories;

use \Illuminate\Support\Collection;

use App\Models\Business\Business;
use App\Domain\Business\Enums\BusinessRoleEnum;

interface BusinessRepositoryInterface
{
    public function findById(int $id): ?Business;

    public function findByUserId(int $userId): Collection;

    public function save(array $data): Business;

    public function existsOwner(int $userId): bool;

    public function update(Business $business, array $data): Business;

    public function delete(Business $business): void;

    public function allWithRelations(): Collection;

    public function attachUser(Business $business, int $userId, BusinessRoleEnum $role): void;
}
