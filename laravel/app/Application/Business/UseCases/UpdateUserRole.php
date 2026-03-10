<?php

namespace App\Application\Business\UseCases;

use App\Repositories\Business\BusinessRepository;
use App\Models\Auth\User;
use App\Notifications\EntityRoleUpdatedNotification;

class UpdateUserRole
{
    public function __construct(private BusinessRepository $repository) {}

    public function execute(int $businessId, int $userId, string $role): bool
    {
        $business = $this->repository->findForManagement($businessId);
        $user = User::findOrFail($userId);

        $currentRole = $business->users()->where('user_id', $userId)->first()?->pivot->role;

        if ($currentRole === $role) {
            return false; // No change needed
        }

        $business->users()->updateExistingPivot($user->id, ['role' => $role]);
        $user->notify(new EntityRoleUpdatedNotification($business, $role));

        return true;
    }
}
