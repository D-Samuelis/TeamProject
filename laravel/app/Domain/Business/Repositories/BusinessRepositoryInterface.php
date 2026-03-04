<?php
namespace App\Domain\Business\Repositories;

use \Illuminate\Support\Collection;

use App\Domain\Business\Entities\Business as DomainBusiness;
use App\Domain\Business\Enums\BusinessRoleEnum;

interface BusinessRepositoryInterface
{
    public function findById(int $id): ?DomainBusiness;

    public function findByUserId(int $userId): array;

    public function save(DomainBusiness $business): DomainBusiness;

    public function update(DomainBusiness $business, array $data): DomainBusiness;

    public function existsOwner(int $userId): bool;

    public function getOwners(int $businessId): array;
 
    public function allWithRelations(): Collection;

    public function attachUser(int $businessId, int $userId, BusinessRoleEnum $role): void;
}