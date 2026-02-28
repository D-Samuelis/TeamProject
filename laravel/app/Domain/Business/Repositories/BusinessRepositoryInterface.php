<?php
namespace App\Domain\Business\Repositories;

use \Illuminate\Support\Collection;

use App\Domain\Business\Entities\Business;

interface BusinessRepositoryInterface
{
    public function findById(int $id): ?Business;

    public function findByUserId(int $userId): array;

    public function create(array $data): Business;

    public function update(Business $business, array $data): Business;

    public function existsOwner(int $userId): bool;

    public function getOwners(int $businessId): array;
 
    public function allWithRelations(): Collection;
}