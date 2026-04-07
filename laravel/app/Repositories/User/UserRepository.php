<?php

namespace App\Repositories\User;

use App\Domain\Branch\Enums\BranchRoleEnum;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Asset;
use App\Models\Business\Branch;
use App\Models\Business\Business;

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
        $member = $user->businesses()->withTrashed()->where('businesses.id', $business->id)->first();

        return $member ? BusinessRoleEnum::tryFrom($member->pivot->role) : null;
    }

    public function getBranchRole(User $user, Branch $branch): ?BranchRoleEnum
    {
        $member = $user->branches()->withTrashed()->where('branches.id', $branch->id)->first();

        return $member ? BranchRoleEnum::tryFrom($member->pivot->role) : null;
    }

    public function getAssetRole(User $user, Asset $asset): ?string
    {
        $asset->load(['branch.business', 'services']);

        if ($asset->branch->business) {
            $businessRole = $this->getBusinessRole($user, $asset->branch->business);
            if ($businessRole === BusinessRoleEnum::OWNER)   return 'owner';
            if ($businessRole === BusinessRoleEnum::MANAGER) return 'manager';
        }

        $branchRole = $this->getBranchRole($user, $asset->branch);
        if ($branchRole === BranchRoleEnum::MANAGER) return 'manager';
        if ($branchRole === BranchRoleEnum::STAFF)   return 'staff';

        foreach ($asset->services as $service) {
            $member = $user->services()
                ->where('services.id', $service->id)
                ->first();

            if ($member) return 'staff';
        }

        return null;
    }

    public function getAnyBranchRoleForBusiness(User $user, Business $business): ?BranchRoleEnum
    {
        $branch = $user->branches()
            ->withTrashed()
            ->where('business_id', $business->id)
            ->first();

        return $branch ? BranchRoleEnum::tryFrom($branch->pivot->role) : null;
    }
}
