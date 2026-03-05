<?php

namespace App\Infrastructure\Business\Repositories;

use Illuminate\Support\Collection;

use App\Models\Business\Business;

use App\Domain\Business\Repositories\BusinessRepositoryInterface;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Enums\BusinessStateEnum;

class EloquentBusinessRepository implements BusinessRepositoryInterface
{
    public function findById(int $id): ?Business
    {
        return Business::find($id);
    }

    public function findByUserId(int $userId): Collection
    {
        return Business::whereHas('users', fn($q) => $q->where('user_id', $userId))->get();
    }

    public function save(array $data): Business
    {
        return Business::create($data);
    }

    public function update(Business $business, array $data): Business
    {
        $business->update($data);
        return $business;
    }

    public function delete(Business $business): void
    {
        $business->update([
            'delete_after' => now()->addDays(7),
        ]);

        $business->delete();
    }

    public function existsOwner(int $userId): bool
    {
        return Business::whereHas(
            'users',
            fn($q) =>
            $q->where('user_id', $userId)
                ->wherePivot('role', BusinessRoleEnum::OWNER->value)
        )->exists();
    }

    public function getOwners(Business $business): array
    {
        return $business->users()
            ->wherePivot('role', BusinessRoleEnum::OWNER->value)
            ->pluck('user_id')
            ->all();
    }

    public function allWithRelations(): Collection
    {
        return Business::with([
            'branches',
            'services.branches'
        ])->get();
    }

    public function attachUser(Business $business, int $userId, BusinessRoleEnum $role): void
    {
        $business->users()->attach($userId, ['role' => $role->value]);
    }
}
