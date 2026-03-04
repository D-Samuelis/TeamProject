<?php

namespace App\Infrastructure\User\Repositories;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Models\Auth\User as EloquentUser;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        $eloquent = EloquentUser::find($id);
        return $eloquent ? \App\Infrastructure\Auth\UserMapper::toDomain($eloquent) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $eloquent = EloquentUser::where('email', $email)->first();
        return $eloquent ? \App\Infrastructure\Auth\UserMapper::toDomain($eloquent) : null;
    }

    public function findByIds(array $ids): array
    {
        $collection = EloquentUser::whereIn('id', $ids)->get();
        return $collection->map(fn($e) => \App\Infrastructure\Auth\UserMapper::toDomain($e))->all();
    }

    public function save(User $user): void
    {
        $eloquent = \App\Infrastructure\Auth\UserMapper::toEloquent($user);

        $eloquent->save();

        if (!$user->id) {
            $user->id = $eloquent->id;
        }
    }

    public function existsWithBusinessRole(int $userId, int $businessId, string $role): bool
    {
        return EloquentUser::where('id', $userId)
            ->whereHas('businesses', fn($q) => $q->where('business_id', $businessId)->wherePivot('role', $role))
            ->exists();
    }

    public function existsWithBranchRole(int $userId, int $branchId, string $role): bool
    {
        return EloquentUser::where('id', $userId)
            ->whereHas('branches', fn($q) => $q->where('branch_id', $branchId)->wherePivot('role', $role))
            ->exists();
    }
}
