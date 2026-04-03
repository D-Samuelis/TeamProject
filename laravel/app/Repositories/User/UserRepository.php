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

    /**
     * Check if the user is associated with the business at any level 
     */
    public function isStaffInBusiness(User $user, Business $business): bool
    {
        if ($user->businesses()->where('businesses.id', $business->id)->exists()) {
            return true;
        }

        if ($user->branches()->where('business_id', $business->id)->exists()) {
            return true;
        }

        return $user->morphToMany(\App\Models\Business\BranchService::class, 'model', 'model_has_users')
            ->whereHas('branch', function ($query) use ($business) {
                $query->where('business_id', $business->id);
            })
            ->exists();
    }

    public function getBusinessRole(User $user, Business $business): ?BusinessRoleEnum
    {
        $member = $user->businesses()
            ->where('businesses.id', $business->id)
            ->first();

        return $member ? BusinessRoleEnum::tryFrom($member->pivot->role) : null;
    }

    public function getBranchRole(User $user, Branch $branch): ?BranchRoleEnum
    {
        $member = $user->branches()
            ->withTrashed()
            ->where('branches.id', $branch->id)
            ->first();

        return $member ? BranchRoleEnum::tryFrom($member->pivot->role) : null;
    }

    public function getAssetRole(User $user, Asset $asset): ?string
    {
        // Load asset with branches (via asset_branch) and branch services (via asset_service)
        $asset->load(['branches.business', 'branchServices.branch']);

        // Check via directly assigned branches
        foreach ($asset->branches as $branch) {
            if ($branch->business) {
                $businessRole = $this->getBusinessRole($user, $branch->business);
                if ($businessRole === BusinessRoleEnum::OWNER)   return 'owner';
                if ($businessRole === BusinessRoleEnum::MANAGER) return 'manager';
            }

            $branchRole = $this->getBranchRole($user, $branch);
            if ($branchRole === BranchRoleEnum::MANAGER) return 'manager';
            if ($branchRole === BranchRoleEnum::STAFF)   return 'staff';
        }

        // Check via branch service instances this asset is assigned to
        foreach ($asset->branchServices as $branchService) {
            $branch = $branchService->branch;

            if ($branch?->business) {
                $businessRole = $this->getBusinessRole($user, $branch->business);
                if ($businessRole === BusinessRoleEnum::OWNER)   return 'owner';
                if ($businessRole === BusinessRoleEnum::MANAGER) return 'manager';
            }

            if ($branch) {
                $branchRole = $this->getBranchRole($user, $branch);
                if ($branchRole === BranchRoleEnum::MANAGER) return 'manager';
                if ($branchRole === BranchRoleEnum::STAFF)   return 'staff';
            }
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
