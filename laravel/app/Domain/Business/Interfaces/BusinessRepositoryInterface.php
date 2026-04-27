<?php

namespace App\Domain\Business\Interfaces;

use App\Application\DTO\BusinessSearchDTO;
use App\Application\DTO\SearchDTO;
use Illuminate\Support\Collection;
use App\Models\Business\Business;
use App\Models\Auth\User;

use Illuminate\Support\Collection;
use App\Application\DTO\SearchDTO;

use App\Domain\Business\Enums\BusinessRoleEnum;

interface BusinessRepositoryInterface
{
    /**
     * PUBLIC: Find a published business by ID. Throws ModelNotFoundException if not found.
     */
    public function findActive(int $id): Business;

    /**
     * PUBLIC: Search for businesses in the marketplace.
     */
    public function search(BusinessSearchDTO $dto, User $user = null);

    public function publicSearch(SearchDTO $dto);

    /**
     * MANAGEMENT: Find a business (including soft-deleted) for owner/admin actions.
     * Throws ModelNotFoundException if not found.
     */
    public function findForManagement(int $id): Business;

    /**
     * MANAGEMENT: List businesses for a specific user.
     */
    public function listForUser(User $user, string $scope = 'active'): Collection;

    /**
     * DATA PERSISTENCE
     */
    public function save(array $data): Business;

    public function update(Business $business, array $data): Business;

    public function delete(Business $business): void;

    public function restore(Business $business): Business;

    /**
     * ACCESS CONTROL & RELATIONSHIPS
     */
    public function existsOwner(int $userId, ?int $businessId = null): bool;

    public function attachUser(Business $business, int $userId, BusinessRoleEnum $role): void;

    public function detachUser(Business $business, int $userId): int;

    public function count(SearchDTO $dto): int;
}