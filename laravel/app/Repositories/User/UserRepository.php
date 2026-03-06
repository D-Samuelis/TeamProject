<?php

namespace App\Repositories\User;

use App\Models\Auth\User;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Branch\Enums\BranchRoleEnum;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function save(array $data): User
    {
        return User::create($data);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function getBusinessRole(User $user, Business $business): ?BusinessRoleEnum
    {
        $pivot = $user->businesses()->where('business_id', $business->id)->first()?->pivot;
        return $pivot?->role;
    }

    public function getBranchRole(User $user, Branch $branch): ?BranchRoleEnum
    {
        $pivot = $user->branches()->where('branch_id', $branch->id)->first()?->pivot;
        return $pivot?->role;
    }
}
