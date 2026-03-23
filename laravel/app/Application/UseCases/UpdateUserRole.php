<?php

namespace App\Application\UseCases;

use App\Repositories\Business\BusinessRepository;
use App\Models\Auth\User;
use App\Notifications\EntityRoleUpdatedNotification;

class UpdateUserRole
{
    public function __construct(
        private BusinessRepository $businessRepo,
        private \App\Repositories\Branch\BranchRepository $branchRepo,
        private \App\Repositories\Service\ServiceRepository $serviceRepo
    ) {}

    public function execute(int $businessId, int $userId, string $role, string $targetType, int $targetId): bool
    {
        $target = match ($targetType) {
            'business' => $this->businessRepo->findForManagement($businessId),
            'branch'   => $this->branchRepo->findWithinBusiness($targetId, $businessId),
            'service'  => $this->serviceRepo->findWithinBusiness($targetId, $businessId),
        };

        $user = User::findOrFail($userId);
        $currentRole = $target->users()->where('user_id', $userId)->first()?->pivot->role;

        if ($currentRole === $role) return false;

        $target->users()->updateExistingPivot($userId, ['role' => $role]);
        $user->notify(new EntityRoleUpdatedNotification($target, $role));

        return true;
    }
}
