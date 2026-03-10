<?php

namespace App\Application\Business\UseCases;

use App\Repositories\Business\BusinessRepository;
use App\Models\Auth\User;
use App\Notifications\EntityRemovedNotification;
use Exception;

class RemoveUser
{
    public function __construct(private BusinessRepository $repository) {}

    public function execute(int $businessId, int $userId): void
    {
        $business = $this->repository->findForManagement($businessId);
        $user = User::findOrFail($userId);

        $member = $business->users()->where('user_id', $userId)->first();

        if (!$member || $member->pivot->role === 'owner') {
            throw new Exception('Cannot remove the owner or user not found.');
        }

        $user->notify(new EntityRemovedNotification($business));
        $this->repository->detachUser($business, $userId);
    }
}
