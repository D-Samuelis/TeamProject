<?php

namespace App\Domain\Business\Interfaces;

use App\Application\DTO\BusinessSearchDTO;
use App\Application\DTO\SearchDTO;
use Illuminate\Support\Collection;
use App\Models\Business\Business;
use App\Models\Auth\User;
use App\Domain\Business\Enums\BusinessRoleEnum;

interface BusinessRepositoryInterface
{
    /**
     * PUBLIC: Find a business by ID for the public profile page.
     * Must return only published and active data.
     */
    public function findActive(int $id): Business | null;

    /**
     * PUBLIC: Search for businesses in the marketplace.
     */
    public function search(BusinessSearchDTO $dto, User $user = null);

    public function publicSearch(SearchDTO $dto);

    /**
     * MANAGEMENT: Find a business by ID for owner/admin actions.
     * Should include soft-deleted records for restoration or auditing.
     */
    public function findForManagement(int $id): Business | null;

    /**
     * MANAGEMENT: List businesses owned by a specific user.
     */
    public function listForUser(User $user, string $scope = 'active'): Collection;

    /**
     * DATA PERSISTENCE
     */
    public function save(array $data): Business | null;

    public function update(Business $business, array $data): Business | null;

    public function delete(Business $business): void;

    public function restore(Business $business): void;

    /**
     * ACCESS CONTROL & RELATIONSHIPS
     */
    public function existsOwner(int $userId, ?int $businessId = null): bool;

    public function attachUser(Business $business, int $userId, BusinessRoleEnum $role): void;

    public function detachUser(Business $business, int $userId): int;

    public function count(SearchDTO $dto): int;
}
