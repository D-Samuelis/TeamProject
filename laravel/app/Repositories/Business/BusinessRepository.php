<?php

namespace App\Repositories\Business;

use Illuminate\Support\Collection;
use App\Models\Business\Business;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\Business\Enums\BusinessRoleEnum;

class BusinessRepository implements BusinessRepositoryInterface
{
    public function findById(int $id): Business
    {
        return Business::findOrFail($id);
    }

    public function findDeletedById(int $id): Business
    {
        return Business::withTrashed()->findOrFail($id);
    }

    public function findByUserId(int $userId): Collection
    {
        return Business::whereHas('users', fn($q) => $q->where('user_id', $userId))->get();
    }

    public function save(array $data): Business
    {
        return Business::create($data);
    }

    public function update(int $id, array $data): Business
    {
        $business = $this->findById($id);
        $business->update($data);
        return $business;
    }

    public function delete(Business $business): void
    {
        $business->update([
            'delete_after' => now()->addDays(7),
            'is_published' => false,
        ]);

        $business->delete();
    }

    public function restore(Business $business): void
    {
        $business->update(['delete_after' => null]);
        $business->restore();
    }

    public function existsOwner(int $userId): bool
    {
        return Business::whereHas('users', fn($q) => $q->where('user_id', $userId)->wherePivot('role', BusinessRoleEnum::OWNER->value))->exists();
    }

    public function getOwners(Business $business): array
    {
        return $business->users()->wherePivot('role', BusinessRoleEnum::OWNER->value)->pluck('user_id')->all();
    }

    public function allWithRelations(string $scope = 'active'): Collection
    {
        $query = Business::with(['branches', 'services.branches']);

        return match ($scope) {
            'active' => $query->get(),
            'deleted' => $query->onlyTrashed()->get(),
            'all' => $query->withTrashed()->get(),
        };
    }

    public function attachUser(Business $business, int $userId, BusinessRoleEnum $role): void
    {
        $business->users()->attach($userId, ['role' => $role->value]);
    }

    public function findByIdWithRelations(int $id): Business
    {
        // Eager load everything needed for the "Manage" page
        return Business::with(['branches', 'services.branches'])
            ->withTrashed() // Allow viewing even if it's in the 'grayed out' state
            ->findOrFail($id);
    }
}
